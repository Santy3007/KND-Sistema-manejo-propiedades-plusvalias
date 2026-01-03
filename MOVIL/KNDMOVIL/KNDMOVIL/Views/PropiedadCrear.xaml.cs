using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using Xamarin.Essentials;
using KNDMovil.Services;
using KNDMovil.Models;
using Newtonsoft.Json;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class PropiedadCrear : ContentPage
    {
        private readonly PropiedadService _propiedadService;
        private readonly FileUploadService _fileUploadService;

        public PropiedadCrear()
        {
            InitializeComponent();
            _propiedadService = new PropiedadService();
            _fileUploadService = new FileUploadService();
            LoadProvinciasAsync(); // Cargar provincias al iniciar
            ProvinciaPicker.SelectedIndexChanged += OnProvinciaSelected; // Asignar el evento
        }

        private async void LoadProvinciasAsync()
        {
            try
            {
                var provincias = await _propiedadService.GetProvinciasAsync();
                var provinciasValidas = provincias?.FindAll(p => p != null) ?? new List<Provincia>();
                ProvinciaPicker.ItemsSource = provinciasValidas;
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al cargar provincias: {ex.Message}", "OK");
            }
        }

        private async void OnProvinciaSelected(object sender, EventArgs e)
        {
            var provinciaSeleccionada = ProvinciaPicker.SelectedItem as Provincia;

            if (provinciaSeleccionada == null)
            {
                await DisplayAlert("Error", "Seleccione una provincia válida.", "OK");
                return;
            }

            try
            {
                var ciudades = await _propiedadService.GetCiudadesByProvinciaIdAsync(provinciaSeleccionada.provincia_id);
                CiudadPicker.ItemsSource = ciudades;
                CiudadPicker.IsEnabled = true;
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al cargar ciudades: {ex.Message}", "OK");
            }
        }

        private void OnAreaTerrenoTextChanged(object sender, TextChangedEventArgs e)
        {
            ValidateDecimalInput(sender, e.NewTextValue);
        }

        private void OnAltoTotalTextChanged(object sender, TextChangedEventArgs e)
        {
            ValidateDecimalInput(sender, e.NewTextValue);
        }

        private void ValidateDecimalInput(object sender, string newText)
        {
            if (!string.IsNullOrWhiteSpace(newText))
            {
                bool isValid = decimal.TryParse(newText, out _);
                if (!isValid)
                {
                    ((Entry)sender).Text = string.Empty; // Limpiar el valor si no es válido
                }
            }
        }
        // Variables para almacenar temporalmente las rutas de los archivos seleccionados
        private Stream _imagenStream = null;
        private string _imagenNombre = null;
        private Stream _pdfStream = null;
        private string _pdfNombre = null;

        // Seleccionar imagen (pero no subir aún)
        private async void OnSeleccionarImagenClicked(object sender, EventArgs e)
        {
            try
            {
                var file = await MediaPicker.PickPhotoAsync();
                if (file != null)
                {
                    _imagenStream = await file.OpenReadAsync();
                    _imagenNombre = file.FileName;
                    ImagenesEntry.Text = $"uploads/{_imagenNombre}";
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al seleccionar la imagen: {ex.Message}", "OK");
            }
        }

        // Seleccionar PDF (pero no subir aún)
        private async void OnSeleccionarPlanosClicked(object sender, EventArgs e)
        {
            try
            {
                var file = await FilePicker.PickAsync(new PickOptions
                {
                    FileTypes = FilePickerFileType.Pdf,
                    PickerTitle = "Seleccione el archivo PDF de los planos"
                });

                if (file != null)
                {
                    _pdfStream = await file.OpenReadAsync();
                    _pdfNombre = file.FileName;
                    PlanosEntry.Text = $"uploads/{_pdfNombre}";
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al seleccionar el PDF: {ex.Message}", "OK");
            }
        }

        // Crear propiedad y subir archivos solo en este punto
        private async void OnCrearPropiedadClicked(object sender, EventArgs e)
        {
            try
            {
                var currentUser = KNDMovil.App.UsuarioGlobal;
                if (currentUser == null)
                {
                    await DisplayAlert("Error", "No se encontró el usuario logueado.", "OK");
                    return;
                }

                var provinciaSeleccionada = ProvinciaPicker.SelectedItem as Provincia;
                var ciudadSeleccionada = CiudadPicker.SelectedItem as Ciudad;

                if (provinciaSeleccionada == null || ciudadSeleccionada == null)
                {
                    await DisplayAlert("Error", "Seleccione una provincia y una ciudad válida.", "OK");
                    return;
                }

                var propiedad = new Propiedad
                {
                    per_id = currentUser.per_id,
                    pro_tipo = TipoEntry.Text,
                    pro_provincia = provinciaSeleccionada.provincia_id.ToString(),
                    pro_ciudad = ciudadSeleccionada.ciudad_id.ToString(),
                    pro_direccion = DireccionEntry.Text,
                    pro_descripcion = DescripcionEditor.Text,
                    pro_area_terreno = decimal.TryParse(AreaTerrenoEntry.Text, out decimal areaTerreno) ? areaTerreno.ToString() : "0",
                    pro_alto_total = decimal.TryParse(AltoTotalEntry.Text, out decimal altoTotal) ? altoTotal.ToString() : "0",
                    pro_precio = decimal.TryParse(PrecioEntry.Text, out decimal precio) ? precio.ToString() : "0",
                    pro_baños = int.TryParse(BaniosEntry.Text, out int banios) ? banios : 0,
                    pro_habitaciones = int.TryParse(HabitacionesEntry.Text, out int habitaciones) ? habitaciones : 0,
                    pro_estacionamientos = EstacionamientosEntry.Text,
                    pro_disponibilidad = DisponibilidadPicker.SelectedItem?.ToString(),
                    pro_estado = EstadoPicker.SelectedItem?.ToString(),
                    pro_celular_propietario = CelularPropietarioEntry.Text,
                    pro_nombre_propietario = NombrePropietarioEntry.Text,
                    pro_imagenes = new List<string>(),  // Inicializar lista vacía
                    pro_planos = null,
                    pro_video = VideoEntry.Text
                };

                // Subir imagen solo si hay una seleccionada
                if (_imagenStream != null && !string.IsNullOrEmpty(_imagenNombre))
                {
                    string jsonResponse = await _fileUploadService.UploadFileAsync(_imagenStream, _imagenNombre);

                    // Depuración: Imprimir la respuesta cruda para ver qué está llegando
                    Console.WriteLine($"Respuesta cruda del servidor: {jsonResponse}");

                    if (!string.IsNullOrEmpty(jsonResponse))
                    {
                        try
                        {
                            var json = JsonConvert.DeserializeObject<dynamic>(jsonResponse);

                            // Verificar si el 'status' es "success"
                            if (json.status == "success")
                            {
                                // Limpiar la ruta de la imagen
                                string filePath = json.file.ToString();
                                filePath = filePath.Replace("\\/", "/");  // Reemplazar \/
                                filePath = filePath.Replace("\\", "/");  // Reemplazar barras invertidas
                                filePath = filePath.Trim();              // Eliminar espacios extra

                                // Obtener el nombre del archivo
                                string fileName = System.IO.Path.GetFileName(filePath);

                                // Agregar la ruta limpia a la lista de imágenes
                                propiedad.pro_imagenes.Add($"uploads/{fileName}");

                                Console.WriteLine($"Ruta final agregada: uploads/{fileName}");
                            }
                            else
                            {
                                // Si el status no es "success", mostrar el mensaje de error
                                await DisplayAlert("Error", json.message.ToString(), "OK");
                            }
                        }
                        catch (JsonReaderException ex)
                        {
                            // Si la deserialización falla, mostrar el error
                            await DisplayAlert("Error", $"Error al parsear la respuesta del servidor: {ex.Message}", "OK");
                        }
                        catch (Exception ex)
                        {
                            // Capturar otros errores
                            await DisplayAlert("Error", $"Error inesperado: {ex.Message}", "OK");
                        }
                    }
                }




                // Subir PDF solo si hay uno seleccionado
                if (_pdfStream != null && !string.IsNullOrEmpty(_pdfNombre))
                {
                    string jsonResponse = await _fileUploadService.UploadFileAsync(_pdfStream, _pdfNombre);

                    if (!string.IsNullOrEmpty(jsonResponse))
                    {
                        var json = JsonConvert.DeserializeObject<dynamic>(jsonResponse);
                        if (json.status == "success")
                        {
                            string filePath = json.file.ToString();
                            string fileName = System.IO.Path.GetFileName(filePath);
                            propiedad.pro_planos = $"uploads/{fileName}";
                        }
                        else
                        {
                            await DisplayAlert("Error", json.message.ToString(), "OK");
                        }
                    }
                }

                // Crear la propiedad en el backend
                string response = await _propiedadService.CrearPropiedadAsync(propiedad);
                await DisplayAlert("Resultado", response, "OK");
                await Navigation.PopAsync();
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al crear la propiedad: {ex.Message}", "OK");
            }
        }
    }
}



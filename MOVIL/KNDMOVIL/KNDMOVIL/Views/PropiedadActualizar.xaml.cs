using System;
using System.Collections.Generic;
using System.Linq;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using KNDMovil.Services;
using KNDMovil.Models;
using System.IO;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class PropiedadActualizar : ContentPage
    {
        private readonly PropiedadService _propiedadService;
        private Propiedad _propiedad;  // Propiedad cargada
        private int _propiedadId;
        private readonly FileUploadService _fileUploadService;


        public PropiedadActualizar(int propiedadId)
        {
            InitializeComponent();
            _propiedadService = new PropiedadService();
            _propiedadId = propiedadId;
            LoadProvinciasAsync(); // Cargar provincias al iniciar
            ProvinciaPicker.SelectedIndexChanged += OnProvinciaSelected; // Asignar el evento
            _fileUploadService = new FileUploadService();


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

        protected override async void OnAppearing()
        {
            base.OnAppearing();

            try
            {
                // 1) Cargar la propiedad desde el web service
                _propiedad = await _propiedadService.GetPropiedadByIdAsync(_propiedadId);
                if (_propiedad == null)
                {
                    await DisplayAlert("Error", "No se pudo cargar la propiedad.", "OK");
                    await Navigation.PopAsync();
                    return;
                }

                // 2) Rellenar campos simples
                TipoEntry.Text = _propiedad.pro_tipo;
                DireccionEntry.Text = _propiedad.pro_direccion;
                DescripcionEditor.Text = _propiedad.pro_descripcion;
                AreaTerrenoEntry.Text = _propiedad.pro_area_terreno;
                AltoTotalEntry.Text = _propiedad.pro_alto_total;
                PrecioEntry.Text = _propiedad.pro_precio;
                BaniosEntry.Text = _propiedad.pro_baños.ToString();
                HabitacionesEntry.Text = _propiedad.pro_habitaciones.ToString();
                EstacionamientosEntry.Text = _propiedad.pro_estacionamientos;
                CelularPropietarioEntry.Text = _propiedad.pro_celular_propietario;
                NombrePropietarioEntry.Text = _propiedad.pro_nombre_propietario;
                VideoEntry.Text = _propiedad.pro_video;
                // 3) Cargar provincias en el Picker
                var provincias = await _propiedadService.GetProvinciasAsync();
                ProvinciaPicker.ItemsSource = provincias;
                ProvinciaPicker.SelectedItem = provincias.FirstOrDefault(p =>
                    p.provincia_id.ToString() == _propiedad.pro_provincia ||
                    p.provincia_nombre.Equals(_propiedad.pro_provincia, StringComparison.OrdinalIgnoreCase));

                // 4) Cargar ciudades según la provincia seleccionada
                if (ProvinciaPicker.SelectedItem is Provincia provSeleccionada)
                {
                    var ciudades = await _propiedadService.GetCiudadesByProvinciaIdAsync(provSeleccionada.provincia_id);
                    CiudadPicker.ItemsSource = ciudades;
                    CiudadPicker.IsEnabled = true;
                    CiudadPicker.SelectedItem = ciudades.FirstOrDefault(c =>
                        c.ciudad_id.ToString() == _propiedad.pro_ciudad ||
                        c.ciudad_nombre.Equals(_propiedad.pro_ciudad, StringComparison.OrdinalIgnoreCase));
                }

                // 5) Configurar Pickers para Disponibilidad y Estado
                var opcionesDisponibilidad = new List<string> { "Disponible", "Ocupado", "Reservado" };
                DisponibilidadPicker.ItemsSource = opcionesDisponibilidad;
                DisponibilidadPicker.SelectedItem = _propiedad.pro_disponibilidad;

                var opcionesEstado = new List<string> { "Disponible", "No Disponible" };
                EstadoPicker.ItemsSource = opcionesEstado;
                EstadoPicker.SelectedItem = _propiedad.pro_estado;

                // 6) Multimedia
                // Asegurar que solo se muestren las rutas relativas en el Entry de imágenes
                if (_propiedad.pro_imagenes != null && _propiedad.pro_imagenes.Count > 0)
                {
                    var rutasRelativas = _propiedad.pro_imagenes
                        .Select(img => $"uploads/{Path.GetFileName(img)}")
                        .ToList();
                    ImagenesEntry.Text = string.Join(", ", rutasRelativas);
                }

                if (_pdfStream != null && !string.IsNullOrEmpty(_pdfNombre))
                {
                    _propiedad.pro_planos = $"uploads/{_pdfNombre}";
                    PlanosEntry.Text = _pdfNombre; // Mostrar solo el nombre
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"No se pudo cargar la propiedad: {ex.Message}", "OK");
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
                var file = await Xamarin.Essentials.MediaPicker.PickPhotoAsync();
                if (file != null)
                {
                    _imagenStream = await file.OpenReadAsync();
                    _imagenNombre = file.FileName;
                    // Mostrar la ruta (formato "uploads/archivo.jpg") en el Entry de imágenes
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
                var file = await Xamarin.Essentials.FilePicker.PickAsync(new Xamarin.Essentials.PickOptions
                {
                    FileTypes = Xamarin.Essentials.FilePickerFileType.Pdf,
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


        private async void OnActualizarPropiedadClicked(object sender, EventArgs e)
        {
            if (_propiedad == null)
            {
                await DisplayAlert("Error", "No se puede actualizar. Propiedad no encontrada.", "OK");
                return;
            }

            try
            {
                // Actualizar valores básicos
                _propiedad.pro_tipo = TipoEntry.Text;
                if (ProvinciaPicker.SelectedItem is Provincia prov)
                    _propiedad.pro_provincia = prov.provincia_id.ToString();
                if (CiudadPicker.SelectedItem is Ciudad ciu)
                    _propiedad.pro_ciudad = ciu.ciudad_id.ToString();
                _propiedad.pro_direccion = DireccionEntry.Text;
                _propiedad.pro_descripcion = DescripcionEditor.Text;
                _propiedad.pro_area_terreno = AreaTerrenoEntry.Text;
                _propiedad.pro_alto_total = AltoTotalEntry.Text;
                _propiedad.pro_precio = PrecioEntry.Text;
                _propiedad.pro_baños = string.IsNullOrWhiteSpace(BaniosEntry.Text) ? 0 : int.Parse(BaniosEntry.Text);
                _propiedad.pro_habitaciones = string.IsNullOrWhiteSpace(HabitacionesEntry.Text) ? 0 : int.Parse(HabitacionesEntry.Text);
                _propiedad.pro_estacionamientos = EstacionamientosEntry.Text;
                _propiedad.pro_disponibilidad = DisponibilidadPicker.SelectedItem?.ToString();
                _propiedad.pro_estado = EstadoPicker.SelectedItem?.ToString();
                _propiedad.pro_celular_propietario = CelularPropietarioEntry.Text;
                _propiedad.pro_nombre_propietario = NombrePropietarioEntry.Text;
                _propiedad.pro_video = VideoEntry.Text;

                // Multimedia: Si se ha seleccionado una nueva imagen, subirla y actualizar
                if (_imagenStream != null && !string.IsNullOrEmpty(_imagenNombre))
                {
                    string jsonResponse = await _fileUploadService.UploadFileAsync(_imagenStream, _imagenNombre);
                    if (!string.IsNullOrEmpty(jsonResponse))
                    {
                        try
                        {
                            var json = Newtonsoft.Json.JsonConvert.DeserializeObject<dynamic>(jsonResponse);
                            if (json.status == "success")
                            {
                                string filePath = json.file.ToString();
                                filePath = filePath.Replace("\\/", "/").Replace("\\", "/").Trim();
                                string fileName = System.IO.Path.GetFileName(filePath);
                                // Como _propiedad.pro_imagenes es una lista, limpiar y agregar el nuevo valor
                                _propiedad.pro_imagenes.Clear();
                                _propiedad.pro_imagenes.Add($"uploads/{fileName}");
                            }
                            else
                            {
                                await DisplayAlert("Error", json.message.ToString(), "OK");
                            }
                        }
                        catch (Exception ex)
                        {
                            await DisplayAlert("Error", $"Error al procesar la imagen: {ex.Message}", "OK");
                        }
                    }
                }
                // Multimedia: Si se ha seleccionado un nuevo PDF, subirlo y actualizar
                if (_pdfStream != null && !string.IsNullOrEmpty(_pdfNombre))
                {
                    string jsonResponse = await _fileUploadService.UploadFileAsync(_pdfStream, _pdfNombre);
                    if (!string.IsNullOrEmpty(jsonResponse))
                    {
                        var json = Newtonsoft.Json.JsonConvert.DeserializeObject<dynamic>(jsonResponse);
                        if (json.status == "success")
                        {
                            string filePath = json.file.ToString();
                            string fileName = System.IO.Path.GetFileName(filePath);
                            _propiedad.pro_planos = $"uploads/{fileName}";
                        }
                        else
                        {
                            await DisplayAlert("Error", json.message.ToString(), "OK");
                        }
                    }
                }

                // Consumir el servicio para actualizar la propiedad
                var response = await _propiedadService.ActualizarPropiedadAsync(_propiedad);
                await DisplayAlert("Resultado", response, "OK");

                // Volver a la lista
                await Navigation.PopAsync();
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al actualizar la propiedad: {ex.Message}", "OK");
            }
        }

    }
}

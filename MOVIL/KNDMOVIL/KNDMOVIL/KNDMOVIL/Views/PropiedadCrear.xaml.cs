using System;
using System.Collections.Generic;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using KNDMovil.Services;
using KNDMovil.Models;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class PropiedadCrear : ContentPage
    {
        private readonly PropiedadService _propiedadService;

        public PropiedadCrear()
        {
            InitializeComponent();
            _propiedadService = new PropiedadService();
        }

        private async void OnCrearPropiedadClicked(object sender, EventArgs e)
        {
            try
            {
                // 1) Obtener el usuario logueado (ajusta a tu forma de obtenerlo)
                var currentUser = KNDMovil.App.UsuarioGlobal;
                if (currentUser == null)
                {
                    await DisplayAlert("Error", "No se encontró el usuario logueado.", "OK");
                    return;
                }

                // 2) Crear objeto Propiedad y asignar campos
                var propiedad = new Propiedad
                {
                    // Se asume que la propiedad le pertenece al usuario actual
                    per_id = currentUser.per_id,

                    pro_tipo = TipoEntry.Text,
                    pro_provincia = ProvinciaEntry.Text,
                    pro_ciudad = CiudadEntry.Text,
                    pro_direccion = DireccionEntry.Text,
                    pro_descripcion = DescripcionEditor.Text,

                    // Guardamos como string (según tu modelo)
                    pro_area_terreno = AreaTerrenoEntry.Text,
                    pro_alto_total = AltoTotalEntry.Text,
                    pro_precio = PrecioEntry.Text,

                    // Campos int
                    pro_baños = string.IsNullOrWhiteSpace(BaniosEntry.Text) ? 0 : int.Parse(BaniosEntry.Text),
                    pro_habitaciones = string.IsNullOrWhiteSpace(HabitacionesEntry.Text) ? 0 : int.Parse(HabitacionesEntry.Text),

                    // El resto de strings
                    pro_estacionamientos = EstacionamientosEntry.Text,
                    pro_disponibilidad = DisponibilidadEntry.Text,
                    pro_estado = EstadoEntry.Text,
                    pro_celular_propietario = CelularPropietarioEntry.Text,
                    pro_nombre_propietario = NombrePropietarioEntry.Text,

                    // La lista de imágenes
                    pro_imagenes = new List<string>(),

                    // Planos es un string en tu modelo
                    pro_planos = PlanosEntry.Text
                };

                // Procesar las URLs de imágenes separadas por comas
                if (!string.IsNullOrWhiteSpace(ImagenesEntry.Text))
                {
                    var imagenesArray = ImagenesEntry.Text.Split(',');
                    foreach (var img in imagenesArray)
                    {
                        var trimmed = img.Trim();
                        if (!string.IsNullOrEmpty(trimmed))
                        {
                            propiedad.pro_imagenes.Add(trimmed);
                        }
                    }
                }

                // 3) Consumir el web service
                string response = await _propiedadService.CrearPropiedadAsync(propiedad);

                // 4) Mostrar resultado
                await DisplayAlert("Resultado", response, "OK");

                // 5) Regresar a la lista o donde corresponda
                await Navigation.PopAsync();
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al crear la propiedad: {ex.Message}", "OK");
            }
        }
    }
}

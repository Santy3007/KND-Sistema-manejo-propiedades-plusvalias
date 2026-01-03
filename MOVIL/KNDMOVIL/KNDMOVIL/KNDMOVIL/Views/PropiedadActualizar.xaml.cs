using System;
using System.Collections.Generic;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using KNDMovil.Services;
using KNDMovil.Models;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class PropiedadActualizar : ContentPage
    {
        private readonly PropiedadService _propiedadService;
        private Propiedad _propiedad;  // Aquí guardaremos la propiedad cargada

        private int _propiedadId;

        public PropiedadActualizar(int propiedadId)
        {
            InitializeComponent();
            _propiedadService = new PropiedadService();
            _propiedadId = propiedadId;
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

                // 2) Rellenar campos
                TipoEntry.Text = _propiedad.pro_tipo;
                ProvinciaEntry.Text = _propiedad.pro_provincia;
                CiudadEntry.Text = _propiedad.pro_ciudad;
                DireccionEntry.Text = _propiedad.pro_direccion;
                DescripcionEditor.Text = _propiedad.pro_descripcion;

                AreaTerrenoEntry.Text = _propiedad.pro_area_terreno;
                AltoTotalEntry.Text = _propiedad.pro_alto_total;
                PrecioEntry.Text = _propiedad.pro_precio;

                BaniosEntry.Text = _propiedad.pro_baños.ToString();
                HabitacionesEntry.Text = _propiedad.pro_habitaciones.ToString();
                EstacionamientosEntry.Text = _propiedad.pro_estacionamientos;
                DisponibilidadEntry.Text = _propiedad.pro_disponibilidad;
                EstadoEntry.Text = _propiedad.pro_estado;
                CelularPropietarioEntry.Text = _propiedad.pro_celular_propietario;
                NombrePropietarioEntry.Text = _propiedad.pro_nombre_propietario;

                // Imágenes en un solo Entry, separadas por comas
                if (_propiedad.pro_imagenes != null && _propiedad.pro_imagenes.Count > 0)
                {
                    ImagenesEntry.Text = string.Join(", ", _propiedad.pro_imagenes);
                }

                // Planos (string)
                PlanosEntry.Text = _propiedad.pro_planos;
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"No se pudo cargar la propiedad: {ex.Message}", "OK");
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
                // 1) Actualizar valores
                _propiedad.pro_tipo = TipoEntry.Text;
                _propiedad.pro_provincia = ProvinciaEntry.Text;
                _propiedad.pro_ciudad = CiudadEntry.Text;
                _propiedad.pro_direccion = DireccionEntry.Text;
                _propiedad.pro_descripcion = DescripcionEditor.Text;

                _propiedad.pro_area_terreno = AreaTerrenoEntry.Text;
                _propiedad.pro_alto_total = AltoTotalEntry.Text;
                _propiedad.pro_precio = PrecioEntry.Text;

                _propiedad.pro_baños = string.IsNullOrWhiteSpace(BaniosEntry.Text) ? 0 : int.Parse(BaniosEntry.Text);
                _propiedad.pro_habitaciones = string.IsNullOrWhiteSpace(HabitacionesEntry.Text) ? 0 : int.Parse(HabitacionesEntry.Text);

                _propiedad.pro_estacionamientos = EstacionamientosEntry.Text;
                _propiedad.pro_disponibilidad = DisponibilidadEntry.Text;
                _propiedad.pro_estado = EstadoEntry.Text;
                _propiedad.pro_celular_propietario = CelularPropietarioEntry.Text;
                _propiedad.pro_nombre_propietario = NombrePropietarioEntry.Text;

                // 2) Imágenes: convertir Entry a lista
                _propiedad.pro_imagenes = new List<string>();
                if (!string.IsNullOrWhiteSpace(ImagenesEntry.Text))
                {
                    var imagenesArray = ImagenesEntry.Text.Split(',');
                    foreach (var img in imagenesArray)
                    {
                        var trimmed = img.Trim();
                        if (!string.IsNullOrEmpty(trimmed))
                        {
                            _propiedad.pro_imagenes.Add(trimmed);
                        }
                    }
                }

                // 3) Planos (string)
                _propiedad.pro_planos = PlanosEntry.Text;

                // 4) Consumir el servicio
                var response = await _propiedadService.ActualizarPropiedadAsync(_propiedad);
                await DisplayAlert("Resultado", response, "OK");

                // 5) Volver a la lista
                await Navigation.PopAsync();
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al actualizar la propiedad: {ex.Message}", "OK");
            }
        }
    }
}

using Xamarin.Forms;
using KNDMovil.ViewModels;
using System;
using Xamarin.Essentials;
using System.Linq; // Agregado para usar FirstOrDefault

namespace KNDMovil.Views
{
    public partial class PropiedadDetallePage : ContentPage
    {
        public PropiedadDetallePage(int propiedadId)
        {
            InitializeComponent();
            BindingContext = new PropiedadDetalleViewModel(propiedadId);
        }

        async void OnVerPlanoClicked(object sender, EventArgs e)
        {
            var viewModel = (PropiedadDetalleViewModel)BindingContext;
            if (!string.IsNullOrEmpty(viewModel.Propiedad?.pro_planos))
            {
                await Browser.OpenAsync(viewModel.Propiedad.pro_planos, BrowserLaunchMode.SystemPreferred);
            }
        }

        private async void OnOpenMapsClicked(object sender, EventArgs e)
        {
            var viewModel = (PropiedadDetalleViewModel)BindingContext;
            var address = viewModel?.Propiedad?.pro_direccion;

            if (!string.IsNullOrWhiteSpace(address))
            {
                try
                {
                    // Geocodificar la dirección para obtener la ubicación
                    var locations = await Geocoding.GetLocationsAsync(address);
                    var location = locations?.FirstOrDefault();

                    if (location != null)
                    {
                        await Map.OpenAsync(location, new MapLaunchOptions
                        {
                            Name = "Ubicación de la propiedad",
                            NavigationMode = NavigationMode.Default
                        });
                    }
                    else
                    {
                        await DisplayAlert("Error", "No se pudo geocodificar la dirección.", "OK");
                    }
                }
                catch (Exception ex)
                {
                    await DisplayAlert("Error", $"No se pudo abrir la dirección: {ex.Message}", "OK");
                }
            }
            else
            {
                await DisplayAlert("Aviso", "No hay dirección disponible.", "OK");
            }
        }
        private async void OnSimularCreditoClicked(object sender, EventArgs e)
        {
            var viewModel = (PropiedadDetalleViewModel)BindingContext;
            // Asegúrate de que la propiedad tenga el identificador (por ejemplo, pro_id)
            int propiedadId = viewModel.Propiedad.pro_id;
            await Navigation.PushAsync(new SimulacionCreditoPage(propiedadId));
        }

    }
}

using System;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using KNDMovil.Services;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class PropiedadEliminar : ContentPage
    {
        private readonly PropiedadService _propiedadService;
        private readonly int _propiedadId;

        public PropiedadEliminar(int propiedadId)
        {
            InitializeComponent();
            _propiedadService = new PropiedadService();
            _propiedadId = propiedadId;
        }

        private async void OnEliminarPropiedadClicked(object sender, EventArgs e)
        {
            bool confirm = await DisplayAlert("Confirmar", "¿Realmente deseas eliminar la propiedad?", "Sí", "No");
            if (!confirm) return;

            try
            {
                // Consumir el servicio para eliminar
                var response = await _propiedadService.EliminarPropiedadAsync(_propiedadId);
                await DisplayAlert("Resultado", response, "OK");

                // Volver a la página anterior (MisPropiedadesPage)
                await Navigation.PopAsync();
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Error al eliminar la propiedad: {ex.Message}", "OK");
            }
        }

        private async void OnCancelarClicked(object sender, EventArgs e)
        {
            await Navigation.PopAsync();
        }
    }
}

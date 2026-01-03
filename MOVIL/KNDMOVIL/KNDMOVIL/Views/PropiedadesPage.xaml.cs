using System;
using Xamarin.Forms;
using KNDMovil.Models;
using KNDMovil.ViewModels;

namespace KNDMovil.Views
{
    public partial class PropiedadesPage : ContentPage
    {
        public PropiedadesPage()
        {
            InitializeComponent();
            BindingContext = new PropiedadesViewModel();
            datos();
        }

        public void datos()
        {
            PropiedadesListView.ItemSelected += async (sender, e) =>
            {
                if (PropiedadesListView.SelectedItem != null)
                {
                    var propiedadSeleccionada = PropiedadesListView.SelectedItem as Propiedad;
                    await Navigation.PushAsync(new PropiedadDetallePage(propiedadSeleccionada.pro_id));
                }
            };
        }

        // Método para manejar el cambio en el tipo de propiedad seleccionado
        // Método para manejar el cambio en el tipo de propiedad seleccionado
        private async void OnTipoPropiedadSelected(object sender, EventArgs e)
        {
            var picker = sender as Picker;
            var tipoSeleccionado = picker.SelectedItem as string;

            if (!string.IsNullOrEmpty(tipoSeleccionado))
            {
                var viewModel = (PropiedadesViewModel)BindingContext;

                if (tipoSeleccionado == "Ninguno")
                {
                    // Cargar todas las propiedades si el filtro es "Ninguno"
                    await viewModel.CargarPropiedades();
                }
                else
                {
                    // Filtrar las propiedades por tipo
                    await viewModel.LoadPropiedadesByTipoAsync(tipoSeleccionado);
                }
            }
        }


    }
}

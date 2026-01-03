using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using KNDMovil.Models;
using KNDMovil.Services;
using KNDMOVIL.Models;
using KNDMovil.Views;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class MisPropiedadesPage : ContentPage
    {
        private readonly Usuario _currentUser;
        private List<Propiedad> _propiedades;
        private readonly PropiedadService _propiedadService;

        public MisPropiedadesPage(Usuario usuario, List<Propiedad> propiedades)
        {
            InitializeComponent();

            // Ocultar la barra de navegación nativa
            NavigationPage.SetHasNavigationBar(this, false);

            _currentUser = usuario;
            _propiedades = propiedades ?? new List<Propiedad>();
            _propiedadService = new PropiedadService();

            // Configurar la información del usuario
            UserNameLabel.Text = $"{_currentUser.per_nombre} {_currentUser.per_apellido}";
            UserEmailLabel.Text = _currentUser.per_email;
            UserRoleLabel.Text = $"Rol: {(_currentUser.rol_id == 1 ? "Administrador Premium" : "Usuario Premium")}";

            // Preprocesar las propiedades para asegurar que las imágenes estén listas
            ProcesarPropiedades(_propiedades);

            // Asignar las propiedades a la CollectionView con animación
            Device.BeginInvokeOnMainThread(async () =>
            {
                PropiedadesCollectionView.Opacity = 0;
                PropiedadesCollectionView.ItemsSource = _propiedades;
                await PropiedadesCollectionView.FadeTo(1, 500, Easing.CubicOut);
            });

            // Configurar el evento de actualización
            RefreshView.Command = new Command(async () =>
            {
                await RefreshPropiedades();
                RefreshView.IsRefreshing = false;
            });
        }

        private void ProcesarPropiedades(List<Propiedad> propiedades)
        {
            foreach (var propiedad in propiedades)
            {
                // Asegurar que pro_imagenes no sea null
                if (propiedad.pro_imagenes == null)
                {
                    propiedad.pro_imagenes = new List<string>();
                }

                // Si no hay imágenes, usar imagen predeterminada
                if (propiedad.pro_imagenes.Count == 0)
                {
                    propiedad.pro_imagenes.Add("placeholder_house.png");
                }
            }
        }

        protected override async void OnAppearing()
        {
            base.OnAppearing();

            // Animación de entrada
            this.Opacity = 0;
            await this.FadeTo(1, 300, Easing.CubicOut);

            // Refrescar propiedades si es necesario
            if (_propiedades == null || _propiedades.Count == 0)
            {
                await RefreshPropiedades();
            }
        }

        private async Task RefreshPropiedades()
        {
            try
            {
                // Mostrar indicador de carga
                LoadingOverlay.Opacity = 0;
                LoadingOverlay.IsVisible = true;
                await LoadingOverlay.FadeTo(1, 200);

                LoadingIndicator.IsVisible = true;
                LoadingIndicator.IsRunning = true;

                // Retraso para mejorar la experiencia visual
                await Task.Delay(500);

                List<Propiedad> nuevasPropiedades;
                // Obtener propiedades según el rol del usuario
                if (_currentUser.rol_id == 1) // Administrador
                {
                    nuevasPropiedades = await _propiedadService.GetAllPropiedadesAsync();
                }
                else
                {
                    nuevasPropiedades = await _propiedadService.GetPropiedadesByUsuarioAsync(_currentUser.per_id);
                }

                // Preprocesar las propiedades para asegurar que las imágenes estén listas
                ProcesarPropiedades(nuevasPropiedades);

                // Animar la transición de datos
                await PropiedadesCollectionView.FadeTo(0, 150);

                // Ajustar datos si es necesario
                foreach (var propiedad in nuevasPropiedades)
                {
                    if (string.IsNullOrEmpty(propiedad.pro_tipo))
                    {
                        propiedad.pro_tipo = "Casa";
                    }
                }

                // Actualizar la colección y asignar la lista global
                PropiedadesCollectionView.ItemsSource = nuevasPropiedades;
                await PropiedadesCollectionView.FadeTo(1, 250, Easing.CubicOut);
                KNDMovil.App.PropiedadesGlobales = nuevasPropiedades;
                _propiedades = nuevasPropiedades;
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"No se pudieron cargar sus propiedades: {ex.Message}", "Entendido");
                Console.WriteLine($"Error al cargar propiedades: {ex}");
            }
            finally
            {
                // Ocultar indicador de carga
                LoadingIndicator.IsRunning = false;
                LoadingIndicator.IsVisible = false;
                await LoadingOverlay.FadeTo(0, 200);
                LoadingOverlay.IsVisible = false;
            }
        }

        private async void OnPropiedadSelected(object sender, SelectionChangedEventArgs e)
        {
            if (e.CurrentSelection.FirstOrDefault() is Propiedad selectedPropiedad)
            {
                // Deseleccionar el item y aplicar efecto visual
                PropiedadesCollectionView.SelectedItem = null;
                await this.FadeTo(0.8, 150, Easing.CubicIn);

                // Navegar a la página de detalle de la propiedad
                await Navigation.PushAsync(new PropiedadDetallePage(selectedPropiedad.pro_id));

                await this.FadeTo(1, 250, Easing.CubicOut);
            }
        }

        // Evento para el botón "Editar" en cada propiedad
        private async void Editar_Clicked(object sender, EventArgs e)
        {
            if (sender is Button button && button.BindingContext is Propiedad propiedad)
            {
                // Navegar a la página de actualización de la propiedad (PropiedadActualizar.xaml)
                await Navigation.PushAsync(new PropiedadActualizar(propiedad.pro_id));
            }
        }

        // Evento para el botón "Eliminar" en cada propiedad
        private async void Eliminar_Clicked(object sender, EventArgs e)
        {
            if (sender is Button button && button.BindingContext is Propiedad propiedad)
            {
                // Navegar a la página de eliminación de la propiedad (PropiedadEliminar.xaml)
                await Navigation.PushAsync(new PropiedadEliminar(propiedad.pro_id));
            }
        }

        // Evento para el botón flotante de "Crear" nueva propiedad
        private async void Crear_Clicked(object sender, EventArgs e)
        {
            // Navegar a la página de creación de propiedad (PropiedadCrear.xaml)
            await Navigation.PushAsync(new PropiedadCrear());
        }
    }
}

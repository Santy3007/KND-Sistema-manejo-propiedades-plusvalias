using System;
using System.Collections.Generic;
using KNDMovil.Models;
using KNDMovil.Services;
using KNDMOVIL.Models;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace KNDMovil.Views
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class LoginPage : ContentPage
    {
        private readonly AuthService _authService;

        public LoginPage()
        {
            InitializeComponent();
            _authService = new AuthService();

            // Aplicar efectos visuales
            NavigationPage.SetHasNavigationBar(this, false);
        }

        private async void OnLoginButtonClicked(object sender, EventArgs e)
        {
            // Validar entradas
            if (string.IsNullOrWhiteSpace(EmailEntry.Text) || string.IsNullOrWhiteSpace(PasswordEntry.Text))
            {
                await StatusLabel.FadeTo(0, 0);
                StatusLabel.Text = "Por favor ingrese email y contraseña";
                await StatusLabel.FadeTo(1, 300);
                return;
            }

            // Mostrar indicador de carga
            LoadingIndicator.IsVisible = true;
            LoadingIndicator.IsRunning = true;
            await LoginButton.FadeTo(0.7, 300);
            LoginButton.IsEnabled = false;
            StatusLabel.Text = "";

            try
            {
                // Llamar al servicio de autenticación
                var (success, message, user, propiedades) = await _authService.LoginAsync(EmailEntry.Text, PasswordEntry.Text);
                if (success && user != null)
                {
                    // Guardar propiedades en la aplicación global si es necesario
                    KNDMovil.App.PropiedadesGlobales = propiedades ?? new List<Propiedad>();

                    // Navegar a la página de propiedades del usuario
                    await Navigation.PushAsync(new MisPropiedadesPage(user, propiedades));

                    // Limpiar entradas
                    EmailEntry.Text = string.Empty;
                    PasswordEntry.Text = string.Empty;
                }
                else
                {
                    await StatusLabel.FadeTo(0, 0);
                    StatusLabel.Text = message;
                    await StatusLabel.FadeTo(1, 300);
                }
            }
            catch (Exception ex)
            {
                await StatusLabel.FadeTo(0, 0);
                StatusLabel.Text = $"Error: {ex.Message}";
                await StatusLabel.FadeTo(1, 300);
                Console.WriteLine($"🚨 Error en login: {ex}");
            }
            finally
            {
                // Ocultar indicador de carga
                LoadingIndicator.IsVisible = false;
                LoadingIndicator.IsRunning = false;
                await LoginButton.FadeTo(1, 300);
                LoginButton.IsEnabled = true;
            }
        }
    }
}
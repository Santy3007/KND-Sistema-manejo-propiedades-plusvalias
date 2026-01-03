using Xamarin.Forms;
using KNDMovil.Views;
using KNDMOVIL;
using KNDMovil.Models;
using System.Collections.Generic;
using KNDMOVIL.Models;

namespace KNDMovil
{
    public partial class App : Application
    {
        public static List<Propiedad> PropiedadesGlobales { get; set; } = new List<Propiedad>();
        public static Usuario UsuarioGlobal { get; set; }

        public App()
        {
            InitializeComponent();

            MainPage = new AppShell();

        }

        protected override void OnStart()
        {
            // Handle when app starts
        }

        protected override void OnSleep()
        {
            // Handle when app sleeps
        }

        protected override void OnResume()
        {
            // Handle when app resumes
        }
    }
}
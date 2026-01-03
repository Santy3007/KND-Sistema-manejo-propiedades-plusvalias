using Xamarin.Forms;
using KNDMovil.Views;


namespace KNDMOVIL
{
    public partial class AppShell : Shell
    {
        public AppShell()
        {
            InitializeComponent();

            Routing.RegisterRoute(nameof(MisPropiedadesPage), typeof(MisPropiedadesPage));
        }
    }
}
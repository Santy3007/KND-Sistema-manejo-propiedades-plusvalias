using Foundation;
using UIKit;
using Xamarin.Forms;

namespace KNDMOVIL.iOS
{
    [Register("AppDelegate")]
    public partial class AppDelegate : Xamarin.Forms.Platform.iOS.FormsApplicationDelegate
    {
        public override bool FinishedLaunching(UIApplication app, NSDictionary options)
        {
            Forms.Init();
            LoadApplication(new KNDMovil.App()); // Asegúrate de que el espacio de nombres sea correcto.

            return base.FinishedLaunching(app, options);
        }
    }
}

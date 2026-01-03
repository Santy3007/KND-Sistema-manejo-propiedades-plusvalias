using System;
using System.Globalization;
using Xamarin.Forms;

namespace KNDMovil.Converters
{
    public class AddressToStaticMapConverter : IValueConverter
    {
        // No se requiere clave de API con este servicio.
        public object Convert(object value, Type targetType, object parameter, CultureInfo culture)
        {
            if (value is string address && !string.IsNullOrWhiteSpace(address))
            {
                var encodedAddress = Uri.EscapeDataString(address);
                var staticMapUrl = $"https://staticmap.openstreetmap.de/staticmap.php?center={encodedAddress}&zoom=15&size=400x400&markers={encodedAddress},lightblue1";

                System.Diagnostics.Debug.WriteLine($"[OSM Converter] URL generada: {staticMapUrl}");

                return staticMapUrl;
            }
            return null;
        }

        public object ConvertBack(object value, Type targetType, object parameter, CultureInfo culture)
        {
            throw new NotImplementedException();
        }
    }
}

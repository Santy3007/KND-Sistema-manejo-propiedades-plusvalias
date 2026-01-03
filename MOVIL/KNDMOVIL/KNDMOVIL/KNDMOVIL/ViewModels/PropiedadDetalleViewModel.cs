using Xamarin.Forms;
using System.ComponentModel;
using System.Runtime.CompilerServices;
using KNDMovil.Models;
using KNDMovil.Services;
using System.Threading.Tasks;
using System;

namespace KNDMovil.ViewModels
{
    public class PropiedadDetalleViewModel : INotifyPropertyChanged
    {
        private Propiedad _propiedad;
        private readonly PropiedadService _propiedadService;
        private bool _isBusy;
        private string _errorMessage;

        public Command VerPlanoCommand { get; }
        // Nuevo comando para WhatsApp
        public Command WhatsAppCommand { get; }

        public Propiedad Propiedad
        {
            get => _propiedad;
            set
            {
                _propiedad = value;
                OnPropertyChanged();
                OnPropertyChanged(nameof(HasPlano));
                OnPropertyChanged(nameof(HasImagenes));
            }
        }

        public bool HasPlano => !string.IsNullOrEmpty(Propiedad?.pro_planos);

        public bool HasImagenes => Propiedad?.pro_imagenes != null && Propiedad.pro_imagenes.Count > 0;

        public bool IsBusy
        {
            get => _isBusy;
            set
            {
                _isBusy = value;
                OnPropertyChanged();
            }
        }

        public string ErrorMessage
        {
            get => _errorMessage;
            set
            {
                _errorMessage = value;
                OnPropertyChanged();
            }
        }

        public PropiedadDetalleViewModel(int propiedadId)
        {
            _propiedadService = new PropiedadService();
            VerPlanoCommand = new Command(OnVerPlanoClicked);
            WhatsAppCommand = new Command(OnWhatsAppClicked);  // Inicializa el comando
            _ = CargarPropiedadDetalleAsync(propiedadId);
        }

        private async Task CargarPropiedadDetalleAsync(int propiedadId)
        {
            if (propiedadId <= 0)
            {
                ErrorMessage = "ID de propiedad inválido.";
                System.Diagnostics.Debug.WriteLine($"Error: ID inválido {propiedadId}");
                return;
            }

            if (IsBusy) return;
            IsBusy = true;
            ErrorMessage = string.Empty;

            try
            {
                Propiedad = await _propiedadService.GetPropiedadByIdAsync(propiedadId);

                if (Propiedad == null)
                {
                    ErrorMessage = "No se encontró la propiedad.";
                    System.Diagnostics.Debug.WriteLine($"Propiedad con ID {propiedadId} no encontrada");
                }
                else
                {
                    System.Diagnostics.Debug.WriteLine($"Propiedad cargada: {Propiedad.pro_tipo}, {Propiedad.pro_provincia}, {Propiedad.pro_ciudad}");
                    OnPropertyChanged(nameof(Propiedad));
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"Error al cargar la propiedad: {ex.Message}";
                System.Diagnostics.Debug.WriteLine($"Excepción completa: {ex}");
            }
            finally
            {
                IsBusy = false;
            }
        }

        private async void OnVerPlanoClicked()
        {
            if (!string.IsNullOrEmpty(Propiedad?.pro_planos))
            {
                await Xamarin.Essentials.Browser.OpenAsync(Propiedad.pro_planos, Xamarin.Essentials.BrowserLaunchMode.SystemPreferred);
            }
        }

        // Nuevo método para abrir WhatsApp
        private async void OnWhatsAppClicked()
        {
            if (Propiedad != null && !string.IsNullOrEmpty(Propiedad.pro_celular_propietario))
            {
                // 1. Remover caracteres que no sean dígitos (espacios, guiones, etc.)
                var rawNumber = System.Text.RegularExpressions.Regex.Replace(
                    Propiedad.pro_celular_propietario, @"\D", ""
                );

                // 2. Ver si inicia con '0' y quitarlo (muy común en números ecuatorianos).
                //    Ejemplo: "0960511346" => "960511346"
                if (rawNumber.StartsWith("0"))
                    rawNumber = rawNumber.Substring(1);

                // 3. Agregar el prefijo de país (Ecuador: +593).
                //    Quedaría: "593" + "960511346" => "593960511346"
                var phoneNumber = "593" + rawNumber;

                // Mensaje predeterminado
                var mensaje = "Hola, estoy interesado en la propiedad. ¿Me puede ayudar con una cita o algo así?";

                // Construir la URL de WhatsApp con el prefijo
                var url = $"https://wa.me/{phoneNumber}?text={Uri.EscapeDataString(mensaje)}";

                // Abrir en navegador o en la app de WhatsApp
                await Xamarin.Essentials.Launcher.OpenAsync(url);
            }
        }


        public event PropertyChangedEventHandler PropertyChanged;
        protected virtual void OnPropertyChanged([CallerMemberName] string propertyName = null)
        {
            PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));
        }
    }
}

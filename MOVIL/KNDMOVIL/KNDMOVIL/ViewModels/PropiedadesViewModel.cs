    using System.Collections.ObjectModel;
    using System.ComponentModel;
    using System.Runtime.CompilerServices;
    using System.Threading.Tasks;
    using KNDMovil.Models;
    using KNDMovil.Services;

    namespace KNDMovil.ViewModels
    {
        public class PropiedadesViewModel : INotifyPropertyChanged
        {
            private ObservableCollection<Propiedad> _propiedades;
            private PropiedadService _propiedadService;

            public ObservableCollection<Propiedad> Propiedades
            {
                get => _propiedades;
                set
                {
                    _propiedades = value;
                    OnPropertyChanged();
                }
            }

            public ObservableCollection<string> TiposPropiedades { get; set; }

            public PropiedadesViewModel()
            {
                _propiedadService = new PropiedadService();
                Propiedades = new ObservableCollection<Propiedad>();
            TiposPropiedades = new ObservableCollection<string> { "Ninguno", "Casa", "Departamento", "Terreno", "Local Comercial", "Otro" };

            CargarPropiedades();
            }

        public async Task CargarPropiedades()
        {
            var propiedades = await _propiedadService.GetAllPropiedadesAsync();
            Propiedades.Clear(); // Limpia la lista antes de agregar nuevos datos
            foreach (var propiedad in propiedades)
            {
                Propiedades.Add(propiedad);
            }
        }


        // Método para cargar propiedades filtradas por tipo
        public async Task LoadPropiedadesByTipoAsync(string tipo)
            {
                var propiedades = await _propiedadService.GetPropiedadesByTipoAsync(tipo);
                Propiedades.Clear();
                foreach (var propiedad in propiedades)
                {
                    Propiedades.Add(propiedad);
                }
            }

            public event PropertyChangedEventHandler PropertyChanged;

            protected virtual void OnPropertyChanged([CallerMemberName] string propertyName = null)
            {
                PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));
            }
        }
    }

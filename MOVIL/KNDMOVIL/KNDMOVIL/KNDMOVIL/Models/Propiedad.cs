using Newtonsoft.Json;
using System.Collections.Generic;
using System.ComponentModel;

namespace KNDMovil.Models
{
    public class Propiedad : INotifyPropertyChanged
    {
        [JsonProperty("pro_id")]
        public int pro_id { get; set; }

        [JsonProperty("per_id")]
        public int per_id { get; set; }

        [JsonProperty("pro_tipo")]
        public string pro_tipo { get; set; }

        [JsonProperty("pro_provincia")]
        public string pro_provincia { get; set; }

        [JsonProperty("pro_ciudad")]
        public string pro_ciudad { get; set; }

        [JsonProperty("pro_area_terreno")]
        public string pro_area_terreno { get; set; }

        [JsonProperty("pro_alto_total")]
        public string pro_alto_total { get; set; }

        [JsonProperty("pro_descripcion")]
        public string pro_descripcion { get; set; }

        [JsonProperty("pro_precio")]
        public string pro_precio { get; set; }

        [JsonProperty("pro_estado")]
        public string pro_estado { get; set; }

        [JsonProperty("pro_celular_propietario")]
        public string pro_celular_propietario { get; set; }

        [JsonProperty("pro_nombre_propietario")]
        public string pro_nombre_propietario { get; set; }

        [JsonProperty("pro_disponibilidad")]
        public string pro_disponibilidad { get; set; }

        [JsonProperty("pro_baños")]
        public int pro_baños { get; set; }

        [JsonProperty("pro_habitaciones")]
        public int pro_habitaciones { get; set; }

        [JsonProperty("pro_estacionamientos")]
        public string pro_estacionamientos { get; set; }

        // Aquí la propiedad se mapea directamente a la lista:
        [JsonProperty("pro_imagenes")]
        public List<string> pro_imagenes { get; set; } = new List<string>();

        [JsonProperty("pro_planos")]
        public string pro_planos { get; set; }

        [JsonProperty("pro_direccion")]
        public string pro_direccion { get; set; }

        public bool HasPlano => !string.IsNullOrEmpty(pro_planos);

        public event PropertyChangedEventHandler PropertyChanged;
    }
}

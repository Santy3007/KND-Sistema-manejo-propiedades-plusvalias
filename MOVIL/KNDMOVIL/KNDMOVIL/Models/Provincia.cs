using System;
using System.Collections.Generic;
using System.Text;

namespace KNDMovil.Models
{
    public class Provincia
    {
        public int provincia_id { get; set; }
        public string provincia_nombre { get; set; }
        public override string ToString()
        {
            return provincia_nombre ?? "Sin nombre";
        }

    }

    public class Ciudad
    {
        public int ciudad_id { get; set; }
        public string ciudad_nombre { get; set; }

        public override string ToString()
        {
            return ciudad_nombre ?? "Sin nombre";
        }
        public int provincia_id { get; set; }

    }
}
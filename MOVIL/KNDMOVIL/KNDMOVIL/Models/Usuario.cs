using System;
using System.Collections.Generic;
using System.Text;

namespace KNDMOVIL.Models
{
    public class Usuario
    {
        public int per_id { get; set; }
        public string per_nombre { get; set; }
        public string per_apellido { get; set; }
        public string per_email { get; set; }
        public string per_password { get; set; }
        public int rol_id { get; set; }
        public string per_status { get; set; } // 'A' o 'I'
    }

}

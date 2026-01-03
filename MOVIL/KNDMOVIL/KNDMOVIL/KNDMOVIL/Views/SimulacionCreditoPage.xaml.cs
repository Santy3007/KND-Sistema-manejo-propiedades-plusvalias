using System;
using Xamarin.Forms;
using System.Net.Http;
using System.Threading.Tasks;
using Newtonsoft.Json;
using System.Collections.Generic;

namespace KNDMovil.Views
{
    public partial class SimulacionCreditoPage : ContentPage
    {
        int propiedadId;

        public SimulacionCreditoPage(int propiedadId)
        {
            InitializeComponent();
            this.propiedadId = propiedadId;

            // Cargar datos de instituciones de forma estática (ajusta según tus necesidades)
            InstitucionPicker.Items.Add("Banco Pichincha"); // ID = 1
            InstitucionPicker.Items.Add("Banco Central");    // ID = 2
            InstitucionPicker.SelectedIndex = 0;

            // Seleccionar por defecto "mensual" y "frances"
            FrecuenciaPicker.SelectedIndex = 0;
            MetodoPicker.SelectedIndex = 0;
        }

        async void OnSimularClicked(object sender, EventArgs e)
        {
            // Validar entrada del plazo
            if (string.IsNullOrWhiteSpace(PlazoEntry.Text))
            {
                await DisplayAlert("Error", "Por favor ingresa el plazo en años.", "OK");
                return;
            }

            if (!int.TryParse(PlazoEntry.Text, out int plazo))
            {
                await DisplayAlert("Error", "El plazo debe ser un número.", "OK");
                return;
            }

            // Obtener el ID de la institución según el índice seleccionado (1 para Banco Pichincha, 2 para Banco Central)
            int institucionId = InstitucionPicker.SelectedIndex == 0 ? 1 : 2;
            string frecuencia = FrecuenciaPicker.SelectedItem.ToString(); // "mensual" o "trimestral"
            string metodo = MetodoPicker.SelectedItem.ToString(); // "frances" o "aleman"

            LoadingIndicator.IsVisible = true;
            LoadingIndicator.IsRunning = true;

            try
            {
                var resultado = await SimularCredito(propiedadId, institucionId, plazo, frecuencia, metodo);
                MostrarResultado(resultado);
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", ex.Message, "OK");
            }
            finally
            {
                LoadingIndicator.IsVisible = false;
                LoadingIndicator.IsRunning = false;
            }
        }

        async Task<SimulacionResponse> SimularCredito(int propiedadId, int institucionId, int plazo, string frecuencia, string metodo)
        {
            using (var client = new HttpClient())
            {
                // URL del web service (ajusta la ruta según tu configuración)
                string url = $"http://192.168.1.20/knd/webservices/simulacioncred.php?id={propiedadId}";

                var values = new Dictionary<string, string>
                {
                    { "institucion", institucionId.ToString() },
                    { "plazo", plazo.ToString() },
                    { "frecuencia", frecuencia },
                    { "metodo", metodo }
                };

                var content = new FormUrlEncodedContent(values);
                var response = await client.PostAsync(url, content);
                response.EnsureSuccessStatusCode();
                var responseString = await response.Content.ReadAsStringAsync();

                return JsonConvert.DeserializeObject<SimulacionResponse>(responseString);
            }
        }

        void MostrarResultado(SimulacionResponse resultado)
        {
            ResultadoLayout.Children.Clear();

            if (resultado == null)
            {
                ResultadoLayout.Children.Add(new Label { Text = "No se obtuvo resultado." });
                return;
            }

            ResultadoLayout.Children.Add(new Label { Text = $"Monto de la propiedad: {resultado.montoPropiedad}", FontAttributes = FontAttributes.Bold });
            ResultadoLayout.Children.Add(new Label { Text = $"Tasa: {resultado.tasaInstitucion}%", TextColor = Color.Black });
            ResultadoLayout.Children.Add(new Label { Text = $"Pago Mensual: {resultado.pagoMensual}", TextColor = Color.Black });
            ResultadoLayout.Children.Add(new Label { Text = $"Total a Pagar: {resultado.totalPagar}", TextColor = Color.Black });
            ResultadoLayout.Children.Add(new Label { Text = $"Total de Intereses: {resultado.totalIntereses}", TextColor = Color.Black });

            ResultadoLayout.Children.Add(new Label { Text = "Tabla de Amortización:", FontAttributes = FontAttributes.Bold, TextColor = Color.Black });
            foreach (var fila in resultado.tablaAmortizacion)
            {
                ResultadoLayout.Children.Add(new Label { Text = $"Cuota: {fila.cuota} | Interés: {fila.interes} | Capital: {fila.capital} | Saldo: {fila.saldo}", TextColor = Color.Black });
            }
        }
    }

    // Clases para mapear la respuesta del web service
    public class SimulacionResponse
    {
        public double montoPropiedad { get; set; }
        public double tasaInstitucion { get; set; }
        public double pagoMensual { get; set; }
        public double totalPagar { get; set; }
        public double totalIntereses { get; set; }
        public List<Amortizacion> tablaAmortizacion { get; set; }
    }

    public class Amortizacion
    {
        public double cuota { get; set; }
        public double interes { get; set; }
        public double capital { get; set; }
        public double saldo { get; set; }
    }
}

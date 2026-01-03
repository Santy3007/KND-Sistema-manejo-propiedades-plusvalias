using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using KNDMovil.Models;
using KNDMOVIL.Models;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace KNDMovil.Services
{
    public class AuthService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://192.168.1.20/knd/webservices/";

        public AuthService()
        {
            _httpClient = new HttpClient();
        }

        public async Task<(bool success, string message, Usuario user, List<Propiedad> propiedades)> LoginAsync(string email, string password)
        {
            try
            {
                var data = new
                {
                    per_email = email,
                    per_password = password
                };

                var json = JsonConvert.SerializeObject(data);
                Console.WriteLine($"📤 JSON enviado: {json}");

                var content = new StringContent(json, Encoding.UTF8, "application/json");

                string url = $"{BaseUrl}getLoginApp.php?action=login";
                Console.WriteLine($"🌍 URL: {url}");

                var response = await _httpClient.PostAsync(url, content);
                Console.WriteLine($"📡 Código HTTP: {response.StatusCode}");

                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"📥 Respuesta del servidor: {responseContent}");

                if (response.IsSuccessStatusCode)
                {
                    var jsonResponse = JObject.Parse(responseContent);
                    if (jsonResponse["status"]?.ToString() == "success")
                    {
                        var usuario = jsonResponse["user"]?.ToObject<Usuario>();
                        var propiedades = jsonResponse["propiedades"]?.ToObject<List<Propiedad>>();

                        return (true, "Login exitoso", usuario, propiedades);
                    }
                    else
                    {
                        string errorMessage = jsonResponse["message"]?.ToString() ?? "Error desconocido";
                        Console.WriteLine($"⚠️ Error de login: {errorMessage}");
                        return (false, errorMessage, null, null);
                    }
                }
                else
                {
                    Console.WriteLine($"❌ Error HTTP: {response.StatusCode}");
                    return (false, $"Error de conexión: {response.StatusCode}", null, null);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"🚨 Error al hacer login: {ex.Message}");
                return (false, $"Error: {ex.Message}", null, null);
            }
        }
    }
}
using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Threading.Tasks;
using Newtonsoft.Json.Linq;
using KNDMovil.Models;
using Newtonsoft.Json;
using System.Text;
using KNDMOVIL.Models;

namespace KNDMovil.Services
{
    public class PropiedadService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://192.168.1.20/knd/webservices/";

        public PropiedadService()
        {
            _httpClient = new HttpClient();
        }
        // Método para hacer login
        public async Task<Usuario> LoginAsync(string email, string password)
        {
            try
            {
                var data = new
                {
                    per_email = email,
                    per_password = password
                };

                var json = JsonConvert.SerializeObject(data);
                Console.WriteLine($"📤 JSON enviado: {json}"); // Ver qué se envía

                var content = new StringContent(json, Encoding.UTF8, "application/json");

                string url = $"{BaseUrl}getLoginApp.php?action=login";
                Console.WriteLine($"🌍 URL: {url}"); // Ver la URL del web service

                var response = await _httpClient.PostAsync(url, content);
                Console.WriteLine($"📡 Código HTTP: {response.StatusCode}"); // Ver estado de la respuesta

                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"📥 Respuesta del servidor: {responseContent}"); // Ver qué responde el servidor

                if (response.IsSuccessStatusCode)
                {
                    var jsonResponse = JObject.Parse(responseContent);
                    if (jsonResponse["status"]?.ToString() == "success")
                    {
                        var usuarioData = jsonResponse["user"]?.ToObject<Usuario>();
                        return usuarioData;
                    }
                    else
                    {
                        Console.WriteLine($"⚠️ Error de login: {jsonResponse["message"]?.ToString()}");
                    }
                }
                else
                {
                    Console.WriteLine($"❌ Error HTTP: {response.StatusCode}");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"🚨 Error al hacer login: {ex.Message}");
            }

            return null;
        }


        public async Task<List<Propiedad>> GetAllPropiedadesAsync()
        {
            try
            {
                var response = await _httpClient.GetStringAsync($"{BaseUrl}getAllProperties.php");
                var jsonResponse = JObject.Parse(response);

                if (jsonResponse["status"].ToString() == "success")
                {
                    var propiedades = jsonResponse["data"].ToObject<List<Propiedad>>();
                    return propiedades;
                }
                return new List<Propiedad>();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener propiedades: {ex.Message}");
                return new List<Propiedad>();
            }
        }

        // Método nuevo para obtener propiedades filtradas por tipo
        public async Task<List<Propiedad>> GetPropiedadesByTipoAsync(string tipo)
        {
            try
            {
                var response = await _httpClient.GetStringAsync($"{BaseUrl}getPropertiesByType.php?pro_tipo={tipo}");
                var jsonResponse = JObject.Parse(response);

                if (jsonResponse["status"].ToString() == "success")
                {
                    var propiedades = jsonResponse["data"].ToObject<List<Propiedad>>();
                    return propiedades;
                }
                return new List<Propiedad>();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener propiedades por tipo: {ex.Message}");
                return new List<Propiedad>();
            }
        }

        public async Task<Propiedad> GetPropiedadByIdAsync(int id)
        {
            try
            {
                System.Diagnostics.Debug.WriteLine($"Requesting URL: {BaseUrl}getPropertyDetails.php?id={id}");

                var response = await _httpClient.GetStringAsync($"{BaseUrl}getPropertyDetails.php?id={id}");
                System.Diagnostics.Debug.WriteLine($"Full Server Response: {response}");

                if (response.TrimStart().StartsWith("{"))
                {
                    var jsonResponse = JObject.Parse(response);
                    System.Diagnostics.Debug.WriteLine($"Response Status: {jsonResponse["status"]}");

                    if (jsonResponse["status"].ToString() == "success")
                    {
                        var propiedadData = jsonResponse["data"];
                        System.Diagnostics.Debug.WriteLine($"pro_tipo: {propiedadData["pro_tipo"]}");
                        System.Diagnostics.Debug.WriteLine($"pro_provincia: {propiedadData["pro_provincia"]}");
                        System.Diagnostics.Debug.WriteLine($"pro_ciudad: {propiedadData["pro_ciudad"]}");

                        var propiedad = propiedadData.ToObject<Propiedad>();

                        if (propiedad.pro_imagenes != null)
                        {
                            System.Diagnostics.Debug.WriteLine($"Imágenes Count: {propiedad.pro_imagenes.Count}");
                            foreach (var imagen in propiedad.pro_imagenes)
                            {
                                System.Diagnostics.Debug.WriteLine($"Imagen URL: {imagen}");
                            }
                        }
                        else
                        {
                            System.Diagnostics.Debug.WriteLine("No hay imágenes disponibles.");
                        }

                        return propiedad;
                    }
                    else
                    {
                        System.Diagnostics.Debug.WriteLine($"Error en respuesta: {jsonResponse["message"]}");
                        return null;
                    }
                }
                else
                {
                    System.Diagnostics.Debug.WriteLine("Respuesta no es JSON válido");
                    return null;
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine($"Error Completo: {ex}");
                System.Diagnostics.Debug.WriteLine($"Mensaje de Error: {ex.Message}");
                System.Diagnostics.Debug.WriteLine($"Traza de Pila: {ex.StackTrace}");
                return null;
            }
        }

        public async Task<string> CrearPropiedadAsync(Propiedad propiedad)
        {
            try
            {
                // Serializa el objeto propiedad a JSON
                var json = JsonConvert.SerializeObject(propiedad);
                Console.WriteLine($"📤 JSON enviado: {json}");

                // Crea el contenido de la solicitud con el tipo "application/json"
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                // Construir la URL para la acción "crear"
                string url = $"{BaseUrl}getPropertiesCrud.php?action=crear";
                Console.WriteLine($"🌍 URL: {url}");

                // Realiza la solicitud POST
                var response = await _httpClient.PostAsync(url, content);
                Console.WriteLine($"📡 Código HTTP: {response.StatusCode}");

                // Leer la respuesta del servidor
                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"📥 Respuesta del servidor: {responseContent}");

                return responseContent;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"🚨 Error al crear propiedad: {ex.Message}");
                return $"Error: {ex.Message}";
            }
        }
        public async Task<string> ActualizarPropiedadAsync(Propiedad propiedad)
        {
            try
            {
                // Serializar el objeto de propiedad a JSON
                var json = JsonConvert.SerializeObject(propiedad);
                Console.WriteLine($"📤 JSON enviado para actualizar: {json}");

                // Crear el contenido de la solicitud
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                // Construir la URL con la acción 'actualizar'
                string url = $"{BaseUrl}getPropertiesCrud.php?action=actualizar";
                Console.WriteLine($"🌍 URL de actualización: {url}");

                // Hacer la solicitud POST
                var response = await _httpClient.PostAsync(url, content);
                Console.WriteLine($"📡 Código HTTP: {response.StatusCode}");

                // Leer la respuesta del servidor
                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"📥 Respuesta del servidor: {responseContent}");

                return responseContent;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"🚨 Error al actualizar propiedad: {ex.Message}");
                return $"Error: {ex.Message}";
            }
        }

        public async Task<string> EliminarPropiedadAsync(int proId)
        {
            try
            {
                // Crear el objeto con el ID de la propiedad a eliminar
                var data = new { pro_id = proId };

                // Serializar el objeto a JSON
                var json = JsonConvert.SerializeObject(data);
                Console.WriteLine($"📤 JSON enviado: {json}");

                // Crear el contenido de la solicitud con el tipo de contenido JSON
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                // Construir la URL con la acción "eliminar"
                string url = $"{BaseUrl}getPropertiesCrud.php?action=eliminar";
                Console.WriteLine($"🌍 URL: {url}");

                // Enviar la solicitud POST
                var response = await _httpClient.PostAsync(url, content);
                Console.WriteLine($"📡 Código HTTP: {response.StatusCode}");

                // Leer la respuesta del servidor
                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"📥 Respuesta del servidor: {responseContent}");

                return responseContent;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"🚨 Error al eliminar propiedad: {ex.Message}");
                return $"Error: {ex.Message}";
            }
        }
        // Añadir este método a tu clase PropiedadService
        public async Task<List<Propiedad>> GetPropiedadesByUsuarioAsync(int userId)
        {
            try
            {
                var response = await _httpClient.GetStringAsync($"{BaseUrl}getPropertiesByUser.php?per_id={userId}");
                var jsonResponse = JObject.Parse(response);

                if (jsonResponse["status"].ToString() == "success")
                {
                    var propiedades = jsonResponse["data"].ToObject<List<Propiedad>>();
                    return propiedades;
                }
                return new List<Propiedad>();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener propiedades del usuario: {ex.Message}");
                return new List<Propiedad>();
            }
        }

    }
}

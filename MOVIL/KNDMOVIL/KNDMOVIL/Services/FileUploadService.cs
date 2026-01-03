using System.IO;
using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;
using Xamarin.Essentials;

namespace KNDMovil.Services
{
    public class FileUploadService
    {
        private readonly HttpClient _httpClient;
        private const string apiUrl = "http://192.168.100.60/Knd/webservices/upload_file.php";  // Cambia esto con la URL de tu API de backend

        public FileUploadService()
        {
            _httpClient = new HttpClient();
        }

        // Subir archivo
        public async Task<string> UploadFileAsync(Stream fileStream, string fileName)
        {
            var formData = new MultipartFormDataContent();
            var fileContent = new StreamContent(fileStream);

            // Configurar el tipo de contenido según el archivo
            var fileExtension = Path.GetExtension(fileName)?.ToLower();
            if (fileExtension == ".pdf")
            {
                fileContent.Headers.ContentType = new MediaTypeHeaderValue("application/pdf");
            }
            else if (fileExtension == ".jpg" || fileExtension == ".jpeg" || fileExtension == ".png")
            {
                fileContent.Headers.ContentType = new MediaTypeHeaderValue("image/jpeg");  // O image/png dependiendo del tipo de archivo
            }
            else
            {
                fileContent.Headers.ContentType = new MediaTypeHeaderValue("application/octet-stream");
            }

            formData.Add(fileContent, "file", fileName);

            try
            {
                var response = await _httpClient.PostAsync(apiUrl, formData);
                if (response.IsSuccessStatusCode)
                {
                    var result = await response.Content.ReadAsStringAsync();
                    return result;  // Puedes devolver una URL o un mensaje de éxito.
                }
                else
                {
                    return $"Error al subir el archivo: {response.ReasonPhrase}";
                }
            }
            catch (HttpRequestException ex)
            {
                return $"Error en la solicitud HTTP: {ex.Message}";
            }
            catch (Exception ex)
            {
                return $"Error desconocido: {ex.Message}";
            }
            finally
            {
                // Cierra el stream después de completar el proceso
                fileStream?.Dispose();
            }
        }
    }
}

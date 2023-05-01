using autenticacion_WS.Clases;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json;
namespace autenticacion_WS.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class VerificationController : ControllerBase
    {
        
        [HttpGet(Name = "verification")]
        public IActionResult Get()
        {
            Dictionary<string, string> requestHeaders = new();
            foreach (var header in Request.Headers)
            {
                requestHeaders.Add(header.Key, header.Value);
            }
            //string test = DateTime.Now.ToString("d-MM-yyyy HH:mm:ss");

            string AuthToken = requestHeaders["Authorization"];
            string jsonresp = "";
            var resp = new[]
            {
                new { username = "", message="Error desconocido" }
            };

            try
            {
                string[] elem = AuthToken.Split('-');
                AuthTokenClass authToken = new AuthTokenClass(elem[0], elem[1], elem[2], elem[3]);
                


                if (!authToken.IsTokenValid())
                {
                    resp = new[]
                    {
                        new {username = "", message="El proceso de validacion del token de autenticacion arrojo un resultado negativo"}
                    };
                    return Unauthorized(JsonConvert.SerializeObject(resp));
                }
                resp = new[]
                {
                    new {username = elem[0], message="Verificacion completa"}
                };
                
            }
            catch (Exception)
            {
                resp = new[]
                {
                    new {username = "", message="Token de autenticacion invalido"}
                };
                return BadRequest(JsonConvert.SerializeObject(resp));
            }
            jsonresp = JsonConvert.SerializeObject(resp);
            return Ok(jsonresp);
        }
    }
    
}
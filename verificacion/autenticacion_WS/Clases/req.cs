namespace autenticacion_WS.Clases
{
    public class AuthTokenClass
    {
        
        public string Username { get; set; }
        public string HoraVigencia { get; set; }
        public string SeccionDestino { get; set; }
        public string Id_App { get; set; }
        public bool Valid { get; set; }

        public AuthTokenClass(string User, string HoraUnix, string SeccionDest, string Id)
        {
            Username = User;
            HoraVigencia = HoraUnix;
            SeccionDestino = SeccionDest;
            Id_App = Id;
        }

        public bool IsTokenValid()
        {
            
            try
            {
                long fechaunix = (long)Convert.ToDouble(HoraVigencia);
                DateTime horLog = DateTimeOffset.FromUnixTimeSeconds(fechaunix).LocalDateTime;
                DateTime FechaAct = DateTime.Now;
                if(horLog < FechaAct)
                {
                    return false;
                }
            }
            catch (Exception)
            {
                return false;
            }
            if(SeccionDestino != "ventas" && SeccionDestino != "almacen")
            {
                return false;
            }
            try
            {
                int id = Convert.ToInt32(Id_App);
                if(id < 0 || id > 1000)
                {
                    return false;
                }
            }
            catch (Exception)
            {
                return false;
            }
            
            return true;
        }

        
    }
}

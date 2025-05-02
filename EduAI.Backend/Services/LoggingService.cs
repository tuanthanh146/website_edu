using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.Extensions.Configuration;
using Serilog;
using Serilog.Events;

namespace EduAI.Backend.Services
{
    public class LoggingService
    {
        private readonly ILogger _logger;

        public LoggingService(IConfiguration configuration)
        {
            var logPath = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "logs", "log-.txt");
            
            _logger = new LoggerConfiguration()
                .MinimumLevel.Debug()
                .MinimumLevel.Override("Microsoft", LogEventLevel.Information)
                .Enrich.FromLogContext()
                .WriteTo.File(
                    logPath,
                    rollingInterval: RollingInterval.Day,
                    retainedFileCountLimit: 7,
                    outputTemplate: "{Timestamp:yyyy-MM-dd HH:mm:ss.fff zzz} [{Level:u3}] {Message:lj}{NewLine}{Exception}")
                .WriteTo.Console()
                .CreateLogger();
        }

        public void LogInformation(string message)
        {
            _logger.Information(message);
        }

        public void LogWarning(string message)
        {
            _logger.Warning(message);
        }

        public void LogError(string message, Exception ex = null)
        {
            if (ex != null)
            {
                _logger.Error(ex, message);
            }
            else
            {
                _logger.Error(message);
            }
        }

        public void LogDebug(string message)
        {
            _logger.Debug(message);
        }
    }
} 
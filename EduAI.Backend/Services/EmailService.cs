using System;
using System.Threading.Tasks;
using Microsoft.Extensions.Configuration;
using MailKit.Net.Smtp;
using MailKit.Security;
using MimeKit;

namespace EduAI.Backend.Services
{
    public class EmailService
    {
        private readonly IConfiguration _configuration;
        private readonly string _smtpServer;
        private readonly int _smtpPort;
        private readonly string _smtpUsername;
        private readonly string _smtpPassword;
        private readonly string _fromEmail;
        private readonly string _fromName;

        public EmailService(IConfiguration configuration)
        {
            _configuration = configuration;
            _smtpServer = _configuration["EmailSettings:SmtpServer"];
            _smtpPort = int.Parse(_configuration["EmailSettings:SmtpPort"]);
            _smtpUsername = _configuration["EmailSettings:Username"];
            _smtpPassword = _configuration["EmailSettings:Password"];
            _fromEmail = _configuration["EmailSettings:FromEmail"];
            _fromName = _configuration["EmailSettings:FromName"];
        }

        public async Task SendVerificationEmail(string email, string token)
        {
            var verificationUrl = $"{_configuration["AppSettings:BaseUrl"]}/verify-email?email={email}&token={token}";
            var message = new MimeMessage();
            message.From.Add(new MailboxAddress(_fromName, _fromEmail));
            message.To.Add(new MailboxAddress("", email));
            message.Subject = "Verify your email address";

            var bodyBuilder = new BodyBuilder
            {
                HtmlBody = $@"
                    <h1>Welcome to EduAI!</h1>
                    <p>Please verify your email address by clicking the link below:</p>
                    <p><a href='{verificationUrl}'>Verify Email</a></p>
                    <p>If you did not create an account, please ignore this email.</p>
                "
            };

            message.Body = bodyBuilder.ToMessageBody();

            await SendEmail(message);
        }

        public async Task SendPasswordResetEmail(string email, string token)
        {
            var resetUrl = $"{_configuration["AppSettings:BaseUrl"]}/reset-password?email={email}&token={token}";
            var message = new MimeMessage();
            message.From.Add(new MailboxAddress(_fromName, _fromEmail));
            message.To.Add(new MailboxAddress("", email));
            message.Subject = "Reset your password";

            var bodyBuilder = new BodyBuilder
            {
                HtmlBody = $@"
                    <h1>Password Reset Request</h1>
                    <p>You have requested to reset your password. Click the link below to proceed:</p>
                    <p><a href='{resetUrl}'>Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you did not request a password reset, please ignore this email.</p>
                "
            };

            message.Body = bodyBuilder.ToMessageBody();

            await SendEmail(message);
        }

        private async Task SendEmail(MimeMessage message)
        {
            using (var client = new SmtpClient())
            {
                await client.ConnectAsync(_smtpServer, _smtpPort, SecureSocketOptions.StartTls);
                await client.AuthenticateAsync(_smtpUsername, _smtpPassword);
                await client.SendAsync(message);
                await client.DisconnectAsync(true);
            }
        }
    }
} 
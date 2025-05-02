using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using EduAI.Backend.Models;
using EduAI.Backend.Services;
using System;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Authorization;
using Microsoft.Data.SqlClient;

namespace EduAI.Backend.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class AuthController : ControllerBase
    {
        private readonly SecurityService _securityService;
        private readonly IConfiguration _configuration;

        public AuthController(SecurityService securityService, IConfiguration configuration)
        {
            _securityService = securityService;
            _configuration = configuration;
        }

        [HttpPost("login")]
        public async Task<IActionResult> Login([FromBody] LoginRequest request)
        {
            try
            {
                var user = await _securityService.AuthenticateUser(request.Email, request.Password);
                
                if (user.TwoFactorEnabled)
                {
                    return Ok(new { 
                        success = true, 
                        requiresTwoFactor = true,
                        message = "Two-factor authentication required" 
                    });
                }

                // Generate JWT token
                var token = GenerateJwtToken(user);
                
                return Ok(new { 
                    success = true, 
                    token,
                    user = new {
                        userId = user.UserId,
                        email = user.Email,
                        fullName = user.FullName,
                        role = user.Role
                    }
                });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        [HttpPost("verify-2fa")]
        public async Task<IActionResult> VerifyTwoFactor([FromBody] TwoFactorRequest request)
        {
            try
            {
                var isValid = await _securityService.ValidateTwoFactorCode(request.Secret, request.Code);
                if (!isValid)
                {
                    return BadRequest(new { success = false, message = "Invalid code" });
                }

                // Generate JWT token
                var token = GenerateJwtToken(request.User);
                
                return Ok(new { 
                    success = true, 
                    token,
                    user = new {
                        userId = request.User.UserId,
                        email = request.User.Email,
                        fullName = request.User.FullName,
                        role = request.User.Role
                    }
                });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        [HttpPost("register")]
        public async Task<IActionResult> Register([FromBody] RegisterRequest request)
        {
            try
            {
                var salt = _securityService.GenerateSalt();
                var hashedPassword = _securityService.HashPassword(request.Password, salt);
                var emailToken = _securityService.GenerateToken();

                using (var connection = new SqlConnection(_configuration.GetConnectionString("DefaultConnection")))
                {
                    await connection.OpenAsync();
                    using (var command = new SqlCommand(
                        "INSERT INTO Users (Email, Password, PasswordSalt, FullName, EmailVerificationToken) " +
                        "VALUES (@Email, @Password, @Salt, @FullName, @Token)", connection))
                    {
                        command.Parameters.AddWithValue("@Email", request.Email);
                        command.Parameters.AddWithValue("@Password", hashedPassword);
                        command.Parameters.AddWithValue("@Salt", salt);
                        command.Parameters.AddWithValue("@FullName", request.FullName);
                        command.Parameters.AddWithValue("@Token", emailToken);
                        
                        await command.ExecuteNonQueryAsync();
                    }
                }

                // Send verification email
                await SendVerificationEmail(request.Email, emailToken);

                return Ok(new { success = true, message = "Registration successful. Please check your email for verification." });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        [HttpPost("verify-email")]
        public async Task<IActionResult> VerifyEmail([FromBody] EmailVerificationRequest request)
        {
            try
            {
                using (var connection = new SqlConnection(_configuration.GetConnectionString("DefaultConnection")))
                {
                    await connection.OpenAsync();
                    using (var command = new SqlCommand(
                        "UPDATE Users SET IsEmailVerified = 1, EmailVerificationToken = NULL " +
                        "WHERE Email = @Email AND EmailVerificationToken = @Token", connection))
                    {
                        command.Parameters.AddWithValue("@Email", request.Email);
                        command.Parameters.AddWithValue("@Token", request.Token);
                        
                        var affected = await command.ExecuteNonQueryAsync();
                        if (affected == 0)
                        {
                            return BadRequest(new { success = false, message = "Invalid verification token" });
                        }
                    }
                }

                return Ok(new { success = true, message = "Email verified successfully" });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        [HttpPost("forgot-password")]
        public async Task<IActionResult> ForgotPassword([FromBody] ForgotPasswordRequest request)
        {
            try
            {
                var resetToken = _securityService.GenerateToken();
                var expiry = DateTime.UtcNow.AddHours(1);

                using (var connection = new SqlConnection(_configuration.GetConnectionString("DefaultConnection")))
                {
                    await connection.OpenAsync();
                    using (var command = new SqlCommand(
                        "UPDATE Users SET PasswordResetToken = @Token, PasswordResetExpires = @Expiry " +
                        "WHERE Email = @Email", connection))
                    {
                        command.Parameters.AddWithValue("@Token", resetToken);
                        command.Parameters.AddWithValue("@Expiry", expiry);
                        command.Parameters.AddWithValue("@Email", request.Email);
                        
                        await command.ExecuteNonQueryAsync();
                    }
                }

                // Send password reset email
                await SendPasswordResetEmail(request.Email, resetToken);

                return Ok(new { success = true, message = "Password reset instructions sent to your email" });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        [HttpPost("reset-password")]
        public async Task<IActionResult> ResetPassword([FromBody] ResetPasswordRequest request)
        {
            try
            {
                var salt = _securityService.GenerateSalt();
                var hashedPassword = _securityService.HashPassword(request.NewPassword, salt);

                using (var connection = new SqlConnection(_configuration.GetConnectionString("DefaultConnection")))
                {
                    await connection.OpenAsync();
                    using (var command = new SqlCommand(
                        "UPDATE Users SET Password = @Password, PasswordSalt = @Salt, " +
                        "PasswordResetToken = NULL, PasswordResetExpires = NULL " +
                        "WHERE Email = @Email AND PasswordResetToken = @Token " +
                        "AND PasswordResetExpires > GETDATE()", connection))
                    {
                        command.Parameters.AddWithValue("@Password", hashedPassword);
                        command.Parameters.AddWithValue("@Salt", salt);
                        command.Parameters.AddWithValue("@Email", request.Email);
                        command.Parameters.AddWithValue("@Token", request.Token);
                        
                        var affected = await command.ExecuteNonQueryAsync();
                        if (affected == 0)
                        {
                            return BadRequest(new { success = false, message = "Invalid or expired reset token" });
                        }
                    }
                }

                return Ok(new { success = true, message = "Password reset successful" });
            }
            catch (Exception ex)
            {
                return BadRequest(new { success = false, message = ex.Message });
            }
        }

        private string GenerateJwtToken(User user)
        {
            // Implement JWT token generation
            return "jwt_token_placeholder";
        }

        private async Task SendVerificationEmail(string email, string token)
        {
            // Implement email sending
        }

        private async Task SendPasswordResetEmail(string email, string token)
        {
            // Implement email sending
        }
    }

    public class LoginRequest
    {
        public string Email { get; set; }
        public string Password { get; set; }
    }

    public class RegisterRequest
    {
        public string Email { get; set; }
        public string Password { get; set; }
        public string FullName { get; set; }
    }

    public class TwoFactorRequest
    {
        public User User { get; set; }
        public string Secret { get; set; }
        public string Code { get; set; }
    }

    public class EmailVerificationRequest
    {
        public string Email { get; set; }
        public string Token { get; set; }
    }

    public class ForgotPasswordRequest
    {
        public string Email { get; set; }
    }

    public class ResetPasswordRequest
    {
        public string Email { get; set; }
        public string Token { get; set; }
        public string NewPassword { get; set; }
    }
} 
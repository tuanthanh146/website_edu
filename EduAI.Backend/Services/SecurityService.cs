using System;
using System.Security.Cryptography;
using System.Text;
using Microsoft.Data.SqlClient;
using Microsoft.Extensions.Configuration;
using EduAI.Backend.Models;

namespace EduAI.Backend.Services
{
    public class SecurityService
    {
        private readonly IConfiguration _configuration;
        private readonly string _connectionString;

        public SecurityService(IConfiguration configuration)
        {
            _configuration = configuration;
            _connectionString = _configuration.GetConnectionString("DefaultConnection");
        }

        public string GenerateSalt()
        {
            byte[] salt = new byte[16];
            using (var rng = RandomNumberGenerator.Create())
            {
                rng.GetBytes(salt);
            }
            return Convert.ToBase64String(salt);
        }

        public string HashPassword(string password, string salt)
        {
            using (var sha256 = SHA256.Create())
            {
                var saltedPassword = password + salt;
                var hashedBytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(saltedPassword));
                return Convert.ToBase64String(hashedBytes);
            }
        }

        public bool VerifyPassword(string password, string hashedPassword, string salt)
        {
            return HashPassword(password, salt) == hashedPassword;
        }

        public string GenerateToken()
        {
            return Convert.ToBase64String(Guid.NewGuid().ToByteArray());
        }

        public async Task<User> AuthenticateUser(string email, string password)
        {
            using (var connection = new SqlConnection(_connectionString))
            {
                await connection.OpenAsync();
                using (var command = new SqlCommand(
                    "SELECT * FROM Users WHERE Email = @Email AND IsActive = 1", connection))
                {
                    command.Parameters.AddWithValue("@Email", email);
                    using (var reader = await command.ExecuteReaderAsync())
                    {
                        if (await reader.ReadAsync())
                        {
                            var user = new User
                            {
                                UserId = reader.GetInt32(reader.GetOrdinal("UserId")),
                                Email = reader.GetString(reader.GetOrdinal("Email")),
                                Password = reader.GetString(reader.GetOrdinal("Password")),
                                PasswordSalt = reader.GetString(reader.GetOrdinal("PasswordSalt")),
                                FailedLoginAttempts = reader.GetInt32(reader.GetOrdinal("FailedLoginAttempts")),
                                AccountLockedUntil = reader.IsDBNull(reader.GetOrdinal("AccountLockedUntil")) 
                                    ? null 
                                    : reader.GetDateTime(reader.GetOrdinal("AccountLockedUntil"))
                            };

                            if (user.AccountLockedUntil.HasValue && user.AccountLockedUntil > DateTime.UtcNow)
                            {
                                throw new Exception("Account is locked. Please try again later.");
                            }

                            if (VerifyPassword(password, user.Password, user.PasswordSalt))
                            {
                                // Reset failed attempts
                                await ResetFailedAttempts(user.UserId);
                                return user;
                            }
                            else
                            {
                                // Increment failed attempts
                                await IncrementFailedAttempts(user.UserId);
                                throw new Exception("Invalid credentials");
                            }
                        }
                        throw new Exception("User not found");
                    }
                }
            }
        }

        private async Task ResetFailedAttempts(int userId)
        {
            using (var connection = new SqlConnection(_connectionString))
            {
                await connection.OpenAsync();
                using (var command = new SqlCommand(
                    "UPDATE Users SET FailedLoginAttempts = 0, AccountLockedUntil = NULL, LastLogin = GETDATE() WHERE UserId = @UserId", 
                    connection))
                {
                    command.Parameters.AddWithValue("@UserId", userId);
                    await command.ExecuteNonQueryAsync();
                }
            }
        }

        private async Task IncrementFailedAttempts(int userId)
        {
            using (var connection = new SqlConnection(_connectionString))
            {
                await connection.OpenAsync();
                using (var command = new SqlCommand(
                    "UPDATE Users SET FailedLoginAttempts = FailedLoginAttempts + 1 WHERE UserId = @UserId", 
                    connection))
                {
                    command.Parameters.AddWithValue("@UserId", userId);
                    await command.ExecuteNonQueryAsync();
                }

                // Check if account should be locked
                using (var checkCommand = new SqlCommand(
                    "SELECT FailedLoginAttempts FROM Users WHERE UserId = @UserId", 
                    connection))
                {
                    checkCommand.Parameters.AddWithValue("@UserId", userId);
                    var attempts = (int)await checkCommand.ExecuteScalarAsync();
                    
                    if (attempts >= 5)
                    {
                        using (var lockCommand = new SqlCommand(
                            "UPDATE Users SET AccountLockedUntil = DATEADD(MINUTE, 30, GETDATE()) WHERE UserId = @UserId", 
                            connection))
                        {
                            lockCommand.Parameters.AddWithValue("@UserId", userId);
                            await lockCommand.ExecuteNonQueryAsync();
                        }
                    }
                }
            }
        }

        public async Task<string> GenerateTwoFactorSecret()
        {
            var secret = GenerateToken();
            // Here you would typically use a library like GoogleAuthenticator
            // to generate a QR code and validate TOTP codes
            return secret;
        }

        public async Task<bool> ValidateTwoFactorCode(string secret, string code)
        {
            // Implement TOTP validation using a library like GoogleAuthenticator
            return true; // Placeholder
        }
    }
} 
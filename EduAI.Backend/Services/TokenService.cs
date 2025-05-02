using System;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Extensions.Configuration;
using Microsoft.IdentityModel.Tokens;
using EduAI.Backend.Models;

namespace EduAI.Backend.Services
{
    public class TokenService
    {
        private readonly IConfiguration _configuration;
        private readonly string _jwtSecret;
        private readonly string _jwtIssuer;
        private readonly string _jwtAudience;
        private readonly int _jwtExpiryMinutes;
        private readonly int _refreshTokenExpiryDays;

        public TokenService(IConfiguration configuration)
        {
            _configuration = configuration;
            _jwtSecret = _configuration["JwtSettings:Secret"];
            _jwtIssuer = _configuration["JwtSettings:Issuer"];
            _jwtAudience = _configuration["JwtSettings:Audience"];
            _jwtExpiryMinutes = int.Parse(_configuration["JwtSettings:ExpiryMinutes"]);
            _refreshTokenExpiryDays = int.Parse(_configuration["JwtSettings:RefreshTokenExpiryDays"]);
        }

        public string GenerateJwtToken(User user)
        {
            var securityKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_jwtSecret));
            var credentials = new SigningCredentials(securityKey, SecurityAlgorithms.HmacSha256);

            var claims = new[]
            {
                new Claim(JwtRegisteredClaimNames.Sub, user.UserId.ToString()),
                new Claim(JwtRegisteredClaimNames.Email, user.Email),
                new Claim(JwtRegisteredClaimNames.Name, user.FullName),
                new Claim(ClaimTypes.Role, user.Role),
                new Claim(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString()),
            };

            var token = new JwtSecurityToken(
                issuer: _jwtIssuer,
                audience: _jwtAudience,
                claims: claims,
                expires: DateTime.UtcNow.AddMinutes(_jwtExpiryMinutes),
                signingCredentials: credentials
            );

            return new JwtSecurityTokenHandler().WriteToken(token);
        }

        public string GenerateRefreshToken()
        {
            return Guid.NewGuid().ToString();
        }

        public DateTime GetRefreshTokenExpiry()
        {
            return DateTime.UtcNow.AddDays(_refreshTokenExpiryDays);
        }

        public ClaimsPrincipal GetPrincipalFromExpiredToken(string token)
        {
            var tokenValidationParameters = new TokenValidationParameters
            {
                ValidateAudience = true,
                ValidateIssuer = true,
                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_jwtSecret)),
                ValidateLifetime = false,
                ValidIssuer = _jwtIssuer,
                ValidAudience = _jwtAudience
            };

            var tokenHandler = new JwtSecurityTokenHandler();
            var principal = tokenHandler.ValidateToken(token, tokenValidationParameters, out SecurityToken securityToken);
            
            if (!(securityToken is JwtSecurityToken jwtSecurityToken) || 
                !jwtSecurityToken.Header.Alg.Equals(SecurityAlgorithms.HmacSha256, StringComparison.InvariantCultureIgnoreCase))
            {
                throw new SecurityTokenException("Invalid token");
            }

            return principal;
        }
    }
} 
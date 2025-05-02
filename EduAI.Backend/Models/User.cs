using System;
using System.ComponentModel.DataAnnotations;

namespace EduAI.Backend.Models
{
    public class User
    {
        public int UserId { get; set; }

        [Required]
        [EmailAddress]
        [StringLength(100)]
        public string Email { get; set; } = string.Empty;

        [StringLength(20)]
        public string? PhoneNumber { get; set; }

        [Required]
        [StringLength(100)]
        public string Password { get; set; } = string.Empty;

        [Required]
        [StringLength(100)]
        public string PasswordSalt { get; set; } = string.Empty;

        [StringLength(100)]
        public string? ResetPasswordToken { get; set; }

        public DateTime? ResetPasswordExpiry { get; set; }

        [Required]
        [StringLength(100)]
        public string FullName { get; set; } = string.Empty;

        [StringLength(255)]
        public string? Bio { get; set; }

        [StringLength(255)]
        public string? Location { get; set; }

        [StringLength(255)]
        public string? Website { get; set; }

        [Required]
        [StringLength(255)]
        public string Avatar { get; set; } = string.Empty;

        [StringLength(255)]
        public string? CoverImage { get; set; }

        [StringLength(20)]
        public string Role { get; set; } = "User";

        public bool IsEmailVerified { get; set; }

        [Required]
        [StringLength(100)]
        public string EmailVerificationToken { get; set; } = string.Empty;

        public DateTime? EmailVerificationExpiry { get; set; }

        public bool TwoFactorEnabled { get; set; }

        [Required]
        [StringLength(100)]
        public string TwoFactorSecret { get; set; } = string.Empty;

        public int FailedLoginAttempts { get; set; }

        public DateTime? AccountLockedUntil { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public DateTime? UpdatedAt { get; set; }

        public DateTime? LastLoginAt { get; set; }

        public bool IsActive { get; set; } = true;

        public bool IsDeleted { get; set; } = false;
    }
} 
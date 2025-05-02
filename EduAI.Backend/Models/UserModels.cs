using System.ComponentModel.DataAnnotations;

namespace EduAI.Backend.Models
{
    public class LoginRequest
    {
        [Required(ErrorMessage = "Email is required")]
        [EmailAddress(ErrorMessage = "Invalid email format")]
        public string Email { get; set; }

        [Required(ErrorMessage = "Password is required")]
        [MinLength(6, ErrorMessage = "Password must be at least 6 characters")]
        public string Password { get; set; }
    }

    public class RegisterRequest
    {
        [Required(ErrorMessage = "Email is required")]
        [EmailAddress(ErrorMessage = "Invalid email format")]
        public string Email { get; set; }

        [Required(ErrorMessage = "Password is required")]
        [MinLength(6, ErrorMessage = "Password must be at least 6 characters")]
        [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$", 
            ErrorMessage = "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character")]
        public string Password { get; set; }

        [Required(ErrorMessage = "Full name is required")]
        [StringLength(100, ErrorMessage = "Full name cannot exceed 100 characters")]
        public string FullName { get; set; }
    }

    public class TwoFactorRequest
    {
        [Required(ErrorMessage = "User information is required")]
        public User User { get; set; }

        [Required(ErrorMessage = "Secret is required")]
        public string Secret { get; set; }

        [Required(ErrorMessage = "Code is required")]
        [StringLength(6, MinimumLength = 6, ErrorMessage = "Code must be 6 characters")]
        public string Code { get; set; }
    }

    public class EmailVerificationRequest
    {
        [Required(ErrorMessage = "Email is required")]
        [EmailAddress(ErrorMessage = "Invalid email format")]
        public string Email { get; set; }

        [Required(ErrorMessage = "Token is required")]
        public string Token { get; set; }
    }

    public class ForgotPasswordRequest
    {
        [Required(ErrorMessage = "Email is required")]
        [EmailAddress(ErrorMessage = "Invalid email format")]
        public string Email { get; set; }
    }

    public class ResetPasswordRequest
    {
        [Required(ErrorMessage = "Email is required")]
        [EmailAddress(ErrorMessage = "Invalid email format")]
        public string Email { get; set; }

        [Required(ErrorMessage = "Token is required")]
        public string Token { get; set; }

        [Required(ErrorMessage = "New password is required")]
        [MinLength(6, ErrorMessage = "Password must be at least 6 characters")]
        [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$", 
            ErrorMessage = "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character")]
        public string NewPassword { get; set; }
    }
} 
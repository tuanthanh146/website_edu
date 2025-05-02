using System;
using System.ComponentModel.DataAnnotations;

namespace EduAIAdmin.Models
{
    public class User
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Name { get; set; } = string.Empty;

        [Required]
        [EmailAddress]
        [StringLength(100)]
        public string Email { get; set; } = string.Empty;

        [Required]
        [StringLength(100)]
        public string Password { get; set; } = string.Empty;

        [Required]
        [StringLength(20)]
        public string Role { get; set; } = string.Empty; // "admin", "teacher", "student"

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }
    }
} 
using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Teacher
    {
        public int Id { get; set; }

        [Required]
        public int UserId { get; set; }

        [StringLength(100)]
        public string Specialization { get; set; } = string.Empty;

        [StringLength(500)]
        public string Bio { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("UserId")]
        public User User { get; set; }
    }
} 
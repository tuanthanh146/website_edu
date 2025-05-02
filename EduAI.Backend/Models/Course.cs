using System;
using System.ComponentModel.DataAnnotations;

namespace EduAI.Backend.Models
{
    public class Course
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Name { get; set; }

        [StringLength(500)]
        public string Description { get; set; }

        [StringLength(50)]
        public string Level { get; set; }

        [StringLength(100)]
        public string Title { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }
    }
}
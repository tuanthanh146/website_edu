using Microsoft.EntityFrameworkCore;

namespace EduAI.Backend.Models
{
    public class EduAIDbContext : DbContext
    {
        public EduAIDbContext(DbContextOptions<EduAIDbContext> options) : base(options) { }

        public DbSet<User> Users { get; set; }
        public DbSet<ChatMessage> ChatMessages { get; set; }
        // TODO: Thêm các DbSet cho Course, Lesson, Subject, Question, Answer khi có model
    }
} 
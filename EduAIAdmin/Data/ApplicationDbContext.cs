using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Models;

namespace EduAIAdmin.Data
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
            : base(options)
        {
        }

        // Định nghĩa các DbSet cho các bảng trong database
        public DbSet<User> Users { get; set; }
        public DbSet<Subject> Subjects { get; set; }
        public DbSet<Topic> Topics { get; set; }
        public DbSet<Question> Questions { get; set; }
        public DbSet<Answer> Answers { get; set; }
        public DbSet<Student> Students { get; set; }
        public DbSet<Teacher> Teachers { get; set; }
        public DbSet<Course> Courses { get; set; }
        public DbSet<Lesson> Lessons { get; set; }
        public DbSet<StudentProgress> StudentProgresses { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Configure relationships and constraints
            modelBuilder.Entity<User>()
                .HasIndex(u => u.Email)
                .IsUnique();

            modelBuilder.Entity<Student>()
                .HasOne(s => s.User)
                .WithOne()
                .HasForeignKey<Student>(s => s.UserId);

            modelBuilder.Entity<Teacher>()
                .HasOne(t => t.User)
                .WithOne()
                .HasForeignKey<Teacher>(t => t.UserId);

            modelBuilder.Entity<Course>()
                .HasOne(c => c.Subject)
                .WithMany(s => s.Courses)
                .HasForeignKey(c => c.SubjectId);

            modelBuilder.Entity<Lesson>()
                .HasOne(l => l.Course)
                .WithMany(c => c.Lessons)
                .HasForeignKey(l => l.CourseId);

            modelBuilder.Entity<Topic>()
                .HasOne(t => t.Subject)
                .WithMany(s => s.Topics)
                .HasForeignKey(t => t.SubjectId);

            modelBuilder.Entity<Question>()
                .HasOne(q => q.Topic)
                .WithMany(t => t.Questions)
                .HasForeignKey(q => q.TopicId);

            modelBuilder.Entity<Answer>()
                .HasOne(a => a.Question)
                .WithMany(q => q.Answers)
                .HasForeignKey(a => a.QuestionId);

            modelBuilder.Entity<StudentProgress>()
                .HasOne(sp => sp.Student)
                .WithMany(s => s.Progresses)
                .HasForeignKey(sp => sp.StudentId);

            modelBuilder.Entity<StudentProgress>()
                .HasOne(sp => sp.Lesson)
                .WithMany(l => l.StudentProgresses)
                .HasForeignKey(sp => sp.LessonId);
        }
    }
} 
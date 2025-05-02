using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using System.Threading.Tasks;

namespace EduAIAdmin.Controllers
{
    public class HomeController : Controller
    {
        private readonly ApplicationDbContext _context;

        public HomeController(ApplicationDbContext context)
        {
            _context = context;
        }

        [HttpGet]
        public async Task<IActionResult> Index()
        {
            // Get counts for dashboard
            ViewBag.UserCount = await _context.Users.CountAsync();
            ViewBag.CourseCount = await _context.Courses.CountAsync();
            ViewBag.SubjectCount = await _context.Subjects.CountAsync();
            ViewBag.LessonCount = await _context.Lessons.CountAsync();
            ViewBag.QuestionCount = await _context.Questions.CountAsync();
            ViewBag.AnswerCount = await _context.Answers.CountAsync();
            
            // Get recent users
            ViewBag.RecentUsers = await _context.Users
                .OrderByDescending(u => u.CreatedAt)
                .Take(5)
                .ToListAsync();
            
            // Get recent courses
            ViewBag.RecentCourses = await _context.Courses
                .Include(c => c.Subject)
                .OrderByDescending(c => c.CreatedAt)
                .Take(5)
                .ToListAsync();
                
            return View();
        }
    }
} 
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class LessonsAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public LessonsAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /LessonsAdmin
        public async Task<IActionResult> Index()
        {
            var lessons = await _context.Lessons
                .Include(l => l.Course)
                .ThenInclude(c => c.Subject)
                .OrderBy(l => l.Course.Title)
                .ThenBy(l => l.Order)
                .ToListAsync();
            return View(lessons);
        }

        // GET: /LessonsAdmin/Create
        public IActionResult Create()
        {
            ViewBag.Courses = _context.Courses.Include(c => c.Subject).ToList();
            return View();
        }

        // POST: /LessonsAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(Lesson lesson)
        {
            if (ModelState.IsValid)
            {
                _context.Add(lesson);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Courses = _context.Courses.Include(c => c.Subject).ToList();
            return View(lesson);
        }

        // GET: /LessonsAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var lesson = await _context.Lessons.FindAsync(id);
            if (lesson == null) return NotFound();
            ViewBag.Courses = _context.Courses.Include(c => c.Subject).ToList();
            return View(lesson);
        }

        // POST: /LessonsAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, Lesson lesson)
        {
            if (id != lesson.Id) return NotFound();
            if (ModelState.IsValid)
            {
                lesson.UpdatedAt = System.DateTime.Now;
                _context.Update(lesson);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Courses = _context.Courses.Include(c => c.Subject).ToList();
            return View(lesson);
        }

        // GET: /LessonsAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var lesson = await _context.Lessons
                .Include(l => l.Course)
                .ThenInclude(c => c.Subject)
                .FirstOrDefaultAsync(l => l.Id == id);
            if (lesson == null) return NotFound();
            return View(lesson);
        }

        // POST: /LessonsAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var lesson = await _context.Lessons.FindAsync(id);
            if (lesson != null)
            {
                _context.Lessons.Remove(lesson);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        // GET: /LessonsAdmin/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();
            var lesson = await _context.Lessons
                .Include(l => l.Course)
                .ThenInclude(c => c.Subject)
                .Include(l => l.StudentProgresses)
                .ThenInclude(sp => sp.Student)
                .FirstOrDefaultAsync(l => l.Id == id);
            if (lesson == null) return NotFound();
            return View(lesson);
        }
    }
} 
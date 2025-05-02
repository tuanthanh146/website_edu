using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class CoursesAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public CoursesAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /CoursesAdmin
        public async Task<IActionResult> Index()
        {
            var courses = await _context.Courses.Include(c => c.Subject).ToListAsync();
            return View(courses);
        }

        // GET: /CoursesAdmin/Create
        public IActionResult Create()
        {
            ViewBag.Subjects = _context.Subjects.ToList();
            return View();
        }

        // POST: /CoursesAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(Course course)
        {
            if (ModelState.IsValid)
            {
                _context.Add(course);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Subjects = _context.Subjects.ToList();
            return View(course);
        }

        // GET: /CoursesAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var course = await _context.Courses.FindAsync(id);
            if (course == null) return NotFound();
            ViewBag.Subjects = _context.Subjects.ToList();
            return View(course);
        }

        // POST: /CoursesAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, Course course)
        {
            if (id != course.Id) return NotFound();
            if (ModelState.IsValid)
            {
                _context.Update(course);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Subjects = _context.Subjects.ToList();
            return View(course);
        }

        // GET: /CoursesAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var course = await _context.Courses.Include(c => c.Subject).FirstOrDefaultAsync(c => c.Id == id);
            if (course == null) return NotFound();
            return View(course);
        }

        // POST: /CoursesAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var course = await _context.Courses.FindAsync(id);
            if (course != null)
            {
                _context.Courses.Remove(course);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }
    }
} 
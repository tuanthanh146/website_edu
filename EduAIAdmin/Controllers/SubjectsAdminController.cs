using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class SubjectsAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public SubjectsAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /SubjectsAdmin
        public async Task<IActionResult> Index()
        {
            var subjects = await _context.Subjects.ToListAsync();
            return View(subjects);
        }

        // GET: /SubjectsAdmin/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: /SubjectsAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(Subject subject)
        {
            if (ModelState.IsValid)
            {
                _context.Add(subject);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(subject);
        }

        // GET: /SubjectsAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var subject = await _context.Subjects.FindAsync(id);
            if (subject == null) return NotFound();
            return View(subject);
        }

        // POST: /SubjectsAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, Subject subject)
        {
            if (id != subject.Id) return NotFound();
            if (ModelState.IsValid)
            {
                subject.UpdatedAt = System.DateTime.Now;
                _context.Update(subject);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(subject);
        }

        // GET: /SubjectsAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var subject = await _context.Subjects.FindAsync(id);
            if (subject == null) return NotFound();
            return View(subject);
        }

        // POST: /SubjectsAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var subject = await _context.Subjects.FindAsync(id);
            if (subject != null)
            {
                _context.Subjects.Remove(subject);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        // GET: /SubjectsAdmin/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();
            var subject = await _context.Subjects
                .Include(s => s.Topics)
                .Include(s => s.Courses)
                .FirstOrDefaultAsync(s => s.Id == id);
            if (subject == null) return NotFound();
            return View(subject);
        }
    }
} 
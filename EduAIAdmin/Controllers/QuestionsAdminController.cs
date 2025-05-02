using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class QuestionsAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public QuestionsAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /QuestionsAdmin
        public async Task<IActionResult> Index()
        {
            var questions = await _context.Questions
                .Include(q => q.Topic)
                .ThenInclude(t => t.Subject)
                .Include(q => q.Answers)
                .OrderBy(q => q.Topic.Name)
                .ToListAsync();
            return View(questions);
        }

        // GET: /QuestionsAdmin/Create
        public IActionResult Create()
        {
            ViewBag.Topics = _context.Topics.Include(t => t.Subject).ToList();
            return View();
        }

        // POST: /QuestionsAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(Question question)
        {
            if (ModelState.IsValid)
            {
                _context.Add(question);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Topics = _context.Topics.Include(t => t.Subject).ToList();
            return View(question);
        }

        // GET: /QuestionsAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var question = await _context.Questions.FindAsync(id);
            if (question == null) return NotFound();
            ViewBag.Topics = _context.Topics.Include(t => t.Subject).ToList();
            return View(question);
        }

        // POST: /QuestionsAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, Question question)
        {
            if (id != question.Id) return NotFound();
            if (ModelState.IsValid)
            {
                question.UpdatedAt = System.DateTime.Now;
                _context.Update(question);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Topics = _context.Topics.Include(t => t.Subject).ToList();
            return View(question);
        }

        // GET: /QuestionsAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var question = await _context.Questions
                .Include(q => q.Topic)
                .ThenInclude(t => t.Subject)
                .FirstOrDefaultAsync(q => q.Id == id);
            if (question == null) return NotFound();
            return View(question);
        }

        // POST: /QuestionsAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var question = await _context.Questions.FindAsync(id);
            if (question != null)
            {
                _context.Questions.Remove(question);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        // GET: /QuestionsAdmin/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();
            var question = await _context.Questions
                .Include(q => q.Topic)
                .ThenInclude(t => t.Subject)
                .Include(q => q.Answers)
                .FirstOrDefaultAsync(q => q.Id == id);
            if (question == null) return NotFound();
            return View(question);
        }
    }
} 
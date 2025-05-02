using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class AnswersAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public AnswersAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /AnswersAdmin
        public async Task<IActionResult> Index()
        {
            var answers = await _context.Answers
                .Include(a => a.Question)
                .OrderBy(a => a.Question.Content)
                .ToListAsync();
            return View(answers);
        }

        // GET: /AnswersAdmin/Create
        public IActionResult Create(int? questionId = null)
        {
            ViewBag.Questions = _context.Questions.ToList();
            if (questionId.HasValue)
            {
                ViewBag.SelectedQuestionId = questionId.Value;
                ViewBag.QuestionContent = _context.Questions.Find(questionId.Value)?.Content;
            }
            return View();
        }

        // POST: /AnswersAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(Answer answer)
        {
            if (ModelState.IsValid)
            {
                _context.Add(answer);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Questions = _context.Questions.ToList();
            return View(answer);
        }

        // GET: /AnswersAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var answer = await _context.Answers.FindAsync(id);
            if (answer == null) return NotFound();
            ViewBag.Questions = _context.Questions.ToList();
            ViewBag.QuestionContent = _context.Questions.Find(answer.QuestionId)?.Content;
            return View(answer);
        }

        // POST: /AnswersAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, Answer answer)
        {
            if (id != answer.Id) return NotFound();
            if (ModelState.IsValid)
            {
                answer.UpdatedAt = System.DateTime.Now;
                _context.Update(answer);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            ViewBag.Questions = _context.Questions.ToList();
            ViewBag.QuestionContent = _context.Questions.Find(answer.QuestionId)?.Content;
            return View(answer);
        }

        // GET: /AnswersAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var answer = await _context.Answers
                .Include(a => a.Question)
                .FirstOrDefaultAsync(a => a.Id == id);
            if (answer == null) return NotFound();
            return View(answer);
        }

        // POST: /AnswersAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var answer = await _context.Answers.FindAsync(id);
            if (answer != null)
            {
                _context.Answers.Remove(answer);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        // GET: /AnswersAdmin/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null) return NotFound();
            var answer = await _context.Answers
                .Include(a => a.Question)
                .FirstOrDefaultAsync(a => a.Id == id);
            if (answer == null) return NotFound();
            return View(answer);
        }

        // GET: /AnswersAdmin/QuestionAnswers/5
        public async Task<IActionResult> QuestionAnswers(int? id)
        {
            if (id == null) return NotFound();
            var question = await _context.Questions
                .Include(q => q.Answers)
                .FirstOrDefaultAsync(q => q.Id == id);
            if (question == null) return NotFound();
            ViewBag.Question = question;
            return View(question.Answers);
        }
    }
}
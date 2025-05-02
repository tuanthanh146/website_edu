using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAIAdmin.Data;
using EduAIAdmin.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAIAdmin.Controllers
{
    public class UsersAdminController : Controller
    {
        private readonly ApplicationDbContext _context;
        public UsersAdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: /UsersAdmin
        public async Task<IActionResult> Index()
        {
            var users = await _context.Users.ToListAsync();
            return View(users);
        }

        // GET: /UsersAdmin/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: /UsersAdmin/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(User user)
        {
            if (ModelState.IsValid)
            {
                _context.Add(user);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(user);
        }

        // GET: /UsersAdmin/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null) return NotFound();
            var user = await _context.Users.FindAsync(id);
            if (user == null) return NotFound();
            return View(user);
        }

        // POST: /UsersAdmin/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, User user)
        {
            if (id != user.Id) return NotFound();
            if (ModelState.IsValid)
            {
                _context.Update(user);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(user);
        }

        // GET: /UsersAdmin/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null) return NotFound();
            var user = await _context.Users.FindAsync(id);
            if (user == null) return NotFound();
            return View(user);
        }

        // POST: /UsersAdmin/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var user = await _context.Users.FindAsync(id);
            if (user != null)
            {
                _context.Users.Remove(user);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }
    }
} 
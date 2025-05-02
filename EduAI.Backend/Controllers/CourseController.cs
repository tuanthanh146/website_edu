using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using EduAI.Backend.Models;
using System.Threading.Tasks;

namespace EduAI.Backend.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class CourseController : ControllerBase
    {
        private readonly EduAIDbContext _context;
        public CourseController(EduAIDbContext context) => _context = context;

        [HttpGet]
        public async Task<IActionResult> GetAll() =>
            Ok(await _context.Set<Course>().ToListAsync());

        [HttpGet("{id}")]
        public async Task<IActionResult> GetById(int id)
        {
            var course = await _context.Set<Course>().FindAsync(id);
            if (course == null) return NotFound();
            return Ok(course);
        }

        [HttpPost]
        public async Task<IActionResult> Create([FromBody] Course model)
        {
            _context.Set<Course>().Add(model);
            await _context.SaveChangesAsync();
            return Ok(model);
        }

        [HttpPut("{id}")]
        public async Task<IActionResult> Update(int id, [FromBody] Course model)
        {
            var course = await _context.Set<Course>().FindAsync(id);
            if (course == null) return NotFound();
            course.Name = model.Name;
            course.Description = model.Description;
            course.Level = model.Level;
            course.Title = model.Title;
            await _context.SaveChangesAsync();
            return Ok(course);
        }

        [HttpDelete("{id}")]
        public async Task<IActionResult> Delete(int id)
        {
            var course = await _context.Set<Course>().FindAsync(id);
            if (course == null) return NotFound();
            _context.Set<Course>().Remove(course);
            await _context.SaveChangesAsync();
            return Ok();
        }
    }
} 
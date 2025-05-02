using Microsoft.AspNetCore.Mvc;
using EduAI.Backend.Models;
using System.Threading.Tasks;
using System.Linq;

namespace EduAI.Backend.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class ChatBotController : ControllerBase
    {
        private readonly EduAIDbContext _context;
        public ChatBotController(EduAIDbContext context) => _context = context;

        [HttpPost("message")]
        public async Task<IActionResult> SendMessage([FromBody] ChatMessage model)
        {
            _context.ChatMessages.Add(model);
            await _context.SaveChangesAsync();
            var reply = $"Bot trả lời: {model.Message}";
            return Ok(new { reply });
        }

        [HttpGet("history/{userId}")]
        public IActionResult GetHistory(string userId)
        {
            var history = _context.ChatMessages
                .Where(x => x.UserId == userId)
                .OrderBy(x => x.Timestamp)
                .ToList();
            return Ok(history);
        }
    }
} 
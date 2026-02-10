<?php
namespace App\Services;

use App\Models\FaqEntry;

class BotService
{
    /**
     * Bot válasz generálása kulcsszavak alapján
     *
     * @param string $message
     * @return string|null
     */
    public function generateResponse(string $message): ?string
    {
        $messageLower = strtolower($message);

        // Handoff kérés ellenőrzése
        if ($this->isHandoffRequest($messageLower)) {
            return null; // Nincs bot válasz, human kell
        }

        foreach (FaqEntry::all() as $faq) {
            $keywords = array_map('trim', explode(',', strtolower($faq->keywords)));
            foreach ($keywords as $keyword) {
                if (strpos($messageLower, $keyword) !== false) {
                    return $faq->answer;
                }
            }
        }

        // Default válasz, ha nincs találat
        return "Sajnálom, nem találtam választ a kérdésedre. Írj 'agent' szót, ha emberi segítséget szeretnél.";
    }

    /**
     * Ellenőrzi, hogy a user emberi segítséget kér-e
     */
    protected function isHandoffRequest(string $message): bool
    {
        $handoffKeywords = ['agent', 'human', 'ember', 'ügyintéző', 'support'];
        foreach ($handoffKeywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}

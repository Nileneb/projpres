# Weekly Mini-Game Platform

A Laravel 12 + Livewire application for student team challenges.  
Built as part of the *Grundlagen Programmieren – Datenbank Modell* assignment.

---

## 🎯 Concept

- Every **week** (`week_label`) students are assigned randomly to **teams** (4 members each).
- Each team **creates one challenge** and **solves another team’s challenge** within **20 minutes**.
- **Submissions** (e.g. link, file URL) are uploaded by the solver team.
- Afterwards, all **other users** can **vote (1–5)** and leave a comment.
- **Scoring rule**: creator and solver teams both receive the same points based on votes.

---

## 🗄 Database Schema (5 Models incl. `User`)

- **users** – authentication (Breeze + Livewire).
- **teams** – groups of 4 per `week_label`.
- **participants** – link table (User ↔ Team), treated as model (`role`, timestamps).
- **matches** – one challenge per creator/solver team pair; tracks challenge text, submission, status.
- **votes** – 1 vote per user per match (excluding creator/solver teams).


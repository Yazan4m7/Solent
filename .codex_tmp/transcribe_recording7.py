import sys

sys.path.insert(0, r"C:\Users\Yazan\Desktop\Projects\Solent\app\.codex_tmp\faster_whisper_deps")

from faster_whisper import WhisperModel


audio_path = r"C:\Users\Yazan\Desktop\Projects\Solent\app\.codex_tmp\recording7-clean.wav"
model = WhisperModel("turbo", device="cpu", compute_type="int8")
segments, info = model.transcribe(
    audio_path,
    language="ar",
    beam_size=5,
    best_of=5,
    vad_filter=False,
    condition_on_previous_text=True,
    initial_prompt=(
        "مكالمة هاتفية باللهجة الأردنية. يزن أبو ليلى من كورفيون يتحدث عن "
        "نظام إدارة مختبر أسنان، والورق، والواتساب، والمكالمات، والضياع والتأخير."
    ),
)

print(f"language={info.language} probability={info.language_probability:.4f}", flush=True)
for segment in segments:
    print(
        f"[{segment.start:06.2f} --> {segment.end:06.2f}] {segment.text.strip()}",
        flush=True,
    )

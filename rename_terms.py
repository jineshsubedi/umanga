import os

replacements = [
    ("meetingMinutes", "meetingMemos"),
    ("meetingMinute", "meetingMemo"),
    ("MeetingMinute", "MeetingMemo"),
    ("meeting_minutes", "meeting_memos"),
    ("meeting-minutes", "meeting-memos"),
    ("meeting_minute", "meeting_memo"),
    ("meeting-minute", "meeting-memo"),
    ("Meeting Minute", "Memo"),
    ("Meeting minute", "Meeting memo"),
    ("meeting minute", "meeting memo"),
    ("MeetingMinutes", "MeetingMemos"),
    ("MinuteSubmitted", "MemoSubmitted"),
    ("MinuteReviewed", "MemoReviewed"),
    ("MinuteController", "MemoController"),
    ("isClient", "isStaff"),
    ("Client", "Staff"),
    ("client", "staff"),
    ("Minute", "Memo"),
    ("minute", "memo")
]

directories = [
    "app",
    "routes",
    "resources/js",
    "database/seeders"
]

exclude_files = [
    "app/Http/Requests/Auth/LoginRequest.php"
]

def process_file(filepath):
    if any(filepath.endswith(exclude) for exclude in exclude_files):
        return
        
    try:
        with open(filepath, 'r') as f:
            content = f.read()
            
        new_content = content
        for old, new in replacements:
            new_content = new_content.replace(old, new)
            
        if new_content != content:
            with open(filepath, 'w') as f:
                f.write(new_content)
            print(f"Updated {filepath}")
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

for directory in directories:
    if os.path.exists(directory):
        for root, _, files in os.walk(directory):
            for file in files:
                if file.endswith((".php", ".vue", ".js")):
                    process_file(os.path.join(root, file))

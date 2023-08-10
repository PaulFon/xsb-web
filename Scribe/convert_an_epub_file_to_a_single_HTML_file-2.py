import tkinter as tk
from tkinter import filedialog
import ebooklib
from ebooklib import epub

def convert_epub_to_html(epub_file_path, output_file_path):
    book = epub.read_epub(epub_file_path)
    extracted_content = []

    for item in book.get_items_of_type(ebooklib.ITEM_DOCUMENT):
        extracted_content.append(item.content.decode('utf-8'))

    html_content = ''.join(extracted_content)

    with open(output_file_path, 'w', encoding='utf-8') as output_file:
        output_file.write(html_content)

# Create the Tkinter file dialog
root = tk.Tk()
root.withdraw()

# Prompt the user to select an EPUB file
epub_file_path = filedialog.askopenfilename(title="Select EPUB File to upload", filetypes=[('EPUB Files', '*.epub')])

# Check if the user selected a file
if epub_file_path:
    # Prompt the user to select an output file name and location
    output_file_path = filedialog.asksaveasfilename(title="Type a name to Save HTML File", defaultextension='.html', filetypes=[('HTML Files', '*.html')])

    # Check if the user provided an output file name
    if output_file_path:
        # Convert the .epub file to a single HTML file
        convert_epub_to_html(epub_file_path, output_file_path)
        print("Conversion successful!")
    else:
        print("No output file selected.")
else:
    print("No EPUB file selected.")

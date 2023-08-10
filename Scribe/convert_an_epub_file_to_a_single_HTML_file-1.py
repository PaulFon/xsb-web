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

# Specify the path to your .epub file
epub_file_path = 'test.epub'

# Specify the output file path for the HTML file
output_file_path = 'test.html'

# Convert the .epub file to a single HTML file
convert_epub_to_html(epub_file_path, output_file_path)

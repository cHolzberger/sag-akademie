			-------------------------------------------
			JSwoof - The Worlds Fasest FLEX JSON Parser
					Version 1.09
				      By Wayne IV Mike.
			-------------------------------------------


Contents:
---------
1: What is JSwoof.

2: What is JSON.

3: Where can i find some example code.

4: Where can i download the latest version of JSwoof.

5: How to include the library in your own FLEX applications.

6: Contacting The Author.

7: License.

8: Fixes.


1: What is JSwoof:
------------------
JSwoof is the fastest JSON parser for FLEX in the world at present. It is fully compliant
with JSON specification (RFC4627). And is also very lightweight, providing a library of 
functions to decode and encode JSON streams at breakneck speeds.


2: What is JSON:
----------------
JSON (JavaScript Object Notation) is a lightweight data-interchange format. It is easy for humans
to read and write. It is easy for machines to parse and generate. It is based on a subset of the
JavaScript Programming Language, Standard ECMA-262 3rd Edition - December 1999.
            
JSON is a text format that is completely language independent but uses conventions that are
familiar to programmers of the C-family of languages, including C, C++, C#, Java, JavaScript,
Perl, Python, and many others. These properties make JSON an ideal data-interchange language.


3: Where can i find some example code:
--------------------------------------
You can find some example FLEX projects within this zip distribution in a folder called
Examples, this folder contains some simple applications that encode and decode JSON text
streams using the Jswoof library.

You will also find the library file in a folder called Lib.


4: Where can i download the latest version of JSwoof:
-----------------------------------------------------
You can always find the latest version of JSwoof on the following website:

	www.waynemike.co.uk/jswoof

You can also find detailed library documentation on this website.


5: How to include the library in your own FLEX applications.
------------------------------------------------------------

From within the FLEX IDE you will see a tab located on the left hand side
of the screen called 'Flex Navigator' on this tab right click on the folder
name you want to import the JSWOOF library into, and then select 'Properties'
from the popup menu that appears.

Next navigate to 'FLex Build Path' from the list on the left hand side of the
newly opened 'Properties' dialog and then select the 'Libraries' tab.

From this tab click the ‘Add SWC’ button. You will now be presented with another
dialog called 'Add SWC' on this dialog click the 'Browse' button and then locate the
jswoof.swc file on your local machine.

I recommend storing all libraries in the 'libs' folder of your FLEX project. But you
can store them where ever you see fit.


6: Contacting The Author:
-------------------------
Please feel free do send any comments, suggestions or bugs to me at the following email
address:

	jswoof@waynemike.co.uk


7: License:
-----------
Copyright (c) 2008-2009 Wayne IV Mike, AKA. Wayne "dappa" Mike.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


8: Fixes:
---------
v1.09		Fixed a minor bug that would cause numbers that followed some strings to
		be stored in number objects as 'NaN'.


v1.08		The parseString function has been completely rewritten to improve speed
		instead of reading a character at a time. It now reads the target string
		in a series of chunks.

		I have also added support for serializing ArrayCollections previous
		versions could only handle Arrays.


v1.07		Fixed ordering of object elements.


v1.06		Major speed improvement to the array parsing function.


v1.05		Fixed translation of escaped characters during the serialization process.


v1.04		Fixed bug that would occur when parsing escaped unicode characters
		that contained hex decimal character digits. i.e. \u01C5 would not
		translate correctly but \u0041 would be fine. This has now been resolved.


v1.03		Improved speed of the parseString, nextToken, and parseObject
		functions. 


v1.02		Fixed bug that would cause the decoder to skip quotes contained
		within a string.


v1.01		The JSON spec states that "Whitespace can be inserted between
		any pair of tokens" This allows us to format our JSON strings
		in a more readable manner. This feature has now been fully
		implemented in version 1.01 of the library. Something that
		core-lib does not fully support at all.


v1.00		Initial release - stable.

			






<?php
/*
 * EasyTest
 * Copyright (c) 2014 Karl Nack
 *
 * This file is subject to the license terms in the LICENSE file found in the
 * top-level directory of this distribution. No part of this project,
 * including this file, may be copied, modified, propagated, or distributed
 * except according to the terms contained in the LICENSE file.
 */

class TestDiff {
    private $diff;

    /*
     * This is used to ensure that text editors don't trim trailing whitespace
     * from the expected output strings in the tests
     */
    private $ws = '';

    public function setup() {
        $this->diff = new easytest\Diff();
    }

    // helper assertions

    private function assert_diff($from, $to, $expected) {
        $actual = $this->diff->diff($from, $to, 'expected', 'actual');
        assert('$expected === $actual');
    }

    public function test_identical_text() {
        $from = $to = <<<TEXT
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TEXT;
        $expected = <<<EXPECTED
- expected
+ actual

  This is the first line.
  This is the second line.
  This is the third line.
  This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    /*
     * The expected outcome of the next two tests is probably somewhat
     * unexpected, as an empty string is shown as being either added or
     * substracted. However, these two cases would need to be specifically
     * handled, and given the expected usage of comparing differing variable
     * values, will most likely not be encountered in practice.
     */

    public function test_add_text() {
        $from = '';
        $to = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

- $this->ws
+ This is the first line.
+ This is the second line.
+ This is the third line.
+ This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_remove_text() {
        $from = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $to = '';
        $expected = <<<EXPECTED
- expected
+ actual

- This is the first line.
- This is the second line.
- This is the third line.
- This is the fourth line.
+ $this->ws
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_add_beginning() {
        $from = <<<FROM
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
FROM;
        $to = <<<TO
This is an extra line.
This is another extra line.
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

+ This is an extra line.
+ This is another extra line.
  This is the first line.
  This is the second line.
  This is the third line.
  This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_remove_beginning() {
        $from = <<<FROM
This is an extra line.
This is another extra line.
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
FROM;
        $to = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

- This is an extra line.
- This is another extra line.
  This is the first line.
  This is the second line.
  This is the third line.
  This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_add_ending() {
        $from = <<<FROM
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
FROM;
        $to = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
This is an extra line.
This is another extra line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

  This is the first line.
  This is the second line.
  This is the third line.
  This is the fourth line.
+ This is an extra line.
+ This is another extra line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_remove_ending() {
        $from = <<<FROM
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
This is an extra line.
This is another extra line.
FROM;
        $to = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

  This is the first line.
  This is the second line.
  This is the third line.
  This is the fourth line.
- This is an extra line.
- This is another extra line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_add_middle() {
        $from = <<<FROM
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
FROM;
        $to = <<<TO
This is the first line.
This is the second line.
This is an extra line.
This is another extra line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

  This is the first line.
  This is the second line.
+ This is an extra line.
+ This is another extra line.
  This is the third line.
  This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_remove_middle() {
        $from = <<<FROM
This is the first line.
This is the second line.
This is an extra line.
This is another extra line.
This is the third line.
This is the fourth line.
FROM;
        $to = <<<TO
This is the first line.
This is the second line.
This is the third line.
This is the fourth line.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

  This is the first line.
  This is the second line.
- This is an extra line.
- This is another extra line.
  This is the third line.
  This is the fourth line.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    /*
     * The next two tests test multiple changes between two strings, and were
     * taken from examples used in several Wikipedia articles on the topic.
     */

    public function test_changes1() {
        $from = <<<FROM
a
g
t
a
c
g
c
a
FROM;
        $to = <<<TO
t
a
t
g
c
TO;
        $expected = <<<EXPECTED
- expected
+ actual

- a
- g
  t
  a
- c
+ t
  g
  c
- a
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_changes2() {
        $from = <<<FROM
This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress the size of the
changes.

This paragraph contains
text that is outdated.
It will be deleted in the
near future.

It is important to spell
check this dokument. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.
FROM;
        $to = <<<TO
This is an important
notice! It should
therefore be located at
the beginning of this
document!

This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress anything.

It is important to spell
check this document. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.

This paragraph contains
important new additions
to this document.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

+ This is an important
+ notice! It should
+ therefore be located at
+ the beginning of this
+ document!
+ $this->ws
  This part of the
  document has stayed the
  same from version to
  version.  It shouldn't
  be shown if it doesn't
  change.  Otherwise, that
  would not be helping to
- compress the size of the
- changes.
- $this->ws
- This paragraph contains
- text that is outdated.
- It will be deleted in the
- near future.
+ compress anything.
  $this->ws
  It is important to spell
- check this dokument. On
+ check this document. On
  the other hand, a
  misspelled word isn't
  the end of the world.
  Nothing in the rest of
  this paragraph needs to
  be changed. Things can
  be added after it.
+ $this->ws
+ This paragraph contains
+ important new additions
+ to this document.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    /*
     * The next two tests are simply repeats of the previous two but with the
     * strings repeated. Although seemingly redundant, initial implementation
     * efforts saw the previous two tests pass while the next two failed,
     * so it would seem to be worthwhile to keep them.
     */

    public function test_changes3() {
        $from = <<<FROM
a
g
t
a
c
g
c
a
a
g
t
a
c
g
c
a
FROM;
        $to = <<<TO
t
a
t
g
c
t
a
t
g
c
TO;
        $expected = <<<EXPECTED
- expected
+ actual

- a
- g
  t
  a
- c
+ t
  g
  c
- a
- a
- g
  t
  a
- c
+ t
  g
  c
- a
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }

    public function test_changes4() {
        $from = <<<FROM
This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress the size of the
changes.

This paragraph contains
text that is outdated.
It will be deleted in the
near future.

It is important to spell
check this dokument. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.

This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress the size of the
changes.

This paragraph contains
text that is outdated.
It will be deleted in the
near future.

It is important to spell
check this dokument. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.
FROM;
        $to = <<<TO
This is an important
notice! It should
therefore be located at
the beginning of this
document!

This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress anything.

It is important to spell
check this document. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.

This paragraph contains
important new additions
to this document.

This is an important
notice! It should
therefore be located at
the beginning of this
document!

This part of the
document has stayed the
same from version to
version.  It shouldn't
be shown if it doesn't
change.  Otherwise, that
would not be helping to
compress anything.

It is important to spell
check this document. On
the other hand, a
misspelled word isn't
the end of the world.
Nothing in the rest of
this paragraph needs to
be changed. Things can
be added after it.

This paragraph contains
important new additions
to this document.
TO;
        $expected = <<<EXPECTED
- expected
+ actual

+ This is an important
+ notice! It should
+ therefore be located at
+ the beginning of this
+ document!
+ $this->ws
  This part of the
  document has stayed the
  same from version to
  version.  It shouldn't
  be shown if it doesn't
  change.  Otherwise, that
  would not be helping to
- compress the size of the
- changes.
- $this->ws
- This paragraph contains
- text that is outdated.
- It will be deleted in the
- near future.
+ compress anything.
  $this->ws
  It is important to spell
- check this dokument. On
+ check this document. On
  the other hand, a
  misspelled word isn't
  the end of the world.
  Nothing in the rest of
  this paragraph needs to
  be changed. Things can
  be added after it.
+ $this->ws
+ This paragraph contains
+ important new additions
+ to this document.
+ $this->ws
+ This is an important
+ notice! It should
+ therefore be located at
+ the beginning of this
+ document!
  $this->ws
  This part of the
  document has stayed the
  same from version to
  version.  It shouldn't
  be shown if it doesn't
  change.  Otherwise, that
  would not be helping to
- compress the size of the
- changes.
- $this->ws
- This paragraph contains
- text that is outdated.
- It will be deleted in the
- near future.
+ compress anything.
  $this->ws
  It is important to spell
- check this dokument. On
+ check this document. On
  the other hand, a
  misspelled word isn't
  the end of the world.
  Nothing in the rest of
  this paragraph needs to
  be changed. Things can
  be added after it.
+ $this->ws
+ This paragraph contains
+ important new additions
+ to this document.
EXPECTED;

        $this->assert_diff($from, $to, $expected);
    }
}

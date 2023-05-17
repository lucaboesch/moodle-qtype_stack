# Answer tests

An _answer test_ is used to compare two expressions to establish whether they satisfy some mathematical criteria. The
prototype test is to establish if they are _algebraically equivalent_.  Informally, the answer tests have the following syntax

    [Errors, Result, FeedBack, Note] = AnswerTest(StudentAnswer, TeacherAnswer, [Opt], [Raw])

Where,

| Variable        | Description
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------
| `StudentAnswer`   | A CAS expression, assumed to be the student's answer.
| `TeacherAnswer`   | A CAS expression, assumed to be the model answer.
| `Opt`             | If needed, any options which the specific answer test provides. For example, a variable, the accuracy of the numerical comparison, number of significant figures.
| `Raw`             | If needed, the raw string of the student's input to ensure, e.g. Maxima does not remove trailing zeros when establishing the number of significant figures.

Since the tests can provide feedback, tests which appear to be symmetrical, e.g. Algebraic Equivalence, really need to assume which expression belongs to the student and which to the teacher.

| Variable  | Description
| --------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Errors    | Hopefully this will be empty!
| Result    | is either `true`, `false`, or `fail` (which indicates a failure of the test).  This determines which branch of the tree is traversed.
| FeedBack  | This is a text string which is displayed to the student. It is [CASText](../CASText.md) which may depend on properties of the student's answer.
| Note      | This is a text string which is used for [Reporting](../Reporting.md). Each answer note is concatenated with the previous notes and any contributions from the branch.

The feedback is only shown to a student if the quiet option is set to 'no'.  We provide a static page giving the outcome of all [answer test results](Results/index.md).  These pages are automatically generated by the STACK unit tests and help illustrate, by examples, the exact behaviour of the tests.  These pages provide examples of feedback provided by each test.

## The tests

Documentation is grouped as follows.

1. [Equivalence](Equivalence.md), e.g. algebraic equivalence (many variations).
  * AlgEquiv
  * AlgEquivNouns
  * SubstEquiv
  * CasEqual
  * SameType
  * SysEquiv
2. [Syntactic form](Form.md), e.g. checking the student's expression is in factored form.
  * FacForm
  * PartFrac
  * SingleFrac
  * CompSquare
  * Expanded
  * LowestTerms
3. [Rules-based](RulesBased.md) tests, e.g. equivalence up to associativity and commutativity and advanced bespoke tests.
  * EqualComAss
  * EqualComAssRules
4. [Numerical tests](Numerical.md) including accuracy, e.g. is written to 3 decimal places.
  * NumRelative
  * NumAbsolute
  * NumSigFigs
  * NumDecPlaces
  * NumDecPlacesWrong
  * SigFigsStrict
  * GT
  * GTE
5. [Scientific](../../Topics/Units.md), e.g. for dealing with dimensional numerical quantities.
  * Units
  * UnitsStrict
  * UnitsRelative
  * UnitsStrictRelative
  * UnitsAbsolute
  * UnitsStrictAbsolute
6. [Calculus](Calculus.md), e.g. for symbolic integration questions.
  * Diff
  * Int
7. [String match tests](String.md) and regular expressions.
  * String
  * StringSloppy
  * [Levenshtein](../../Topics/Levenshtein_distance.md)
  * SRegExp
8. [Other](Other.md) specific subject tests, e.g. sets, logical expressions.
  * Sets
  * [Equiv](../../CAS/Equivalence_reasoning.md)
  * [EquivFirst](../../CAS/Equivalence_reasoning.md)
  * [PropLogic](../../Topics/Propositional_Logic.md)

## Pre-pocessing students' answers ##

You can apply functions before applying the tests using the feedback variables.  For example, to ignore case sensitivity you can apply the [Maxima commands defined by STACK](../../CAS/Maxima.md#Maxima_commands_defined_by_STACK) `exdowncase(ex)` to the arguments, before you apply one of the other answer tests. However, some tests really require the raw student's input.  E.g. the numerical decimal place test really requires the name of an input as the `SAns` field.  If you manipulate an input, you may end up dropping trailing zeros and ruining the number of decimal places in the expression.  STACK will warn you if you need to use the name of an input.

## Offline testing ##

Sometimes it is very useful to work offline using the desktop version of Maxima.  How to do this is explained in the [STACK-maxima-sandbox](../../CAS/STACK-Maxima_sandbox.md).

The Maxima code for each answer test is the name of the test with `AT` in front.  E.g. the algebraic equivalence `AlgEquiv` test is called in the sandbox as `ATAlgEquiv`.  E.g. to test two expressions in the sandbox type

    ATAlgEquiv((x-1)*(x+1), x^2-1);

In the STACK question dashboard, and some of the reporting features, you may see expressions such as the above.  These can be copied into Maxima to help test individual questions.

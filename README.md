RealCoveragePrototype
=====================

Just a prototype!

But...

composer install --prefer-dist --optimize-autoloader && php Permutator/permutate.php && firefox html/index.html


And enjoy what RealCoverage is about ;)

Algorithm
=========
The basic idea:

{{{
for each tested class
    for each line in this class
        comment out the line
        test if any preceeding covered line can be commented out

        if the tests do not run anymore
            comment the line in
            Test if any preceeding covered line can be commented out
        end if
    end for
end for
}}}
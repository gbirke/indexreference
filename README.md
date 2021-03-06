# Index number reference plugin

This DokuWiki syntax plugin allows to reference counters generated by the 
[indexnumber plugin](https://github.com/gbirke/indexnumber). The wiki tags are 
replaced with the generated counter names and values.

## Installation
Copy the `indexreference` folder into the `lib/plugins` folder of your DokuWiki 
installation.

## Tag syntax

Syntax for referencing a counter number is `<idxref countername id>`.  
`countername` must be a valid counter name from an `idxnum` tag.  
`id` must be a numeric id used in an `idxnum` tag. The leading # char can be omitted.

The `idxref` tag will insert the counter prefix and counter value
or an error message if the referenced counter was not found.

### Examples

    For detailed numbers, see <idxref Tab. #1>.

    As you can see in <idxref Fig. 4>, snowmen are nearly invisible in front of 
    white backgrounds.

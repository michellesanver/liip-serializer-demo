# Serializer Generator
Jira ticket: MAPI-3376

The serializer generator produces simplistic PHP functions that handle the conversion between arrays and objects for a
specific combination of serializer groups and one API version. To do this, the generator uses the JMS serializer
annotations on the models. You need to specify which models should have such PHP functions. In the beginning, we do this
for Products only.

The serializer is made available as the class GeneratedSerializer which implements the JMS interfaces. If no generated
file is found or if any error occurs, that serializer falls back to JMS.

## Implementation Overview

Each serialize / deserialize function is in its own file in the symfony cache folder. They are created in the
`cache:warmup` step. There is no monitoring, so whenever you change any of the models that use the new serializer, or
when working on the serializer code itself, you need to recreate those files. To avoid having to call `cache:warmup` too
often, there is the command `liip:serializer:generate` in the dev environment.

The code hopefully speaks for itself. You can look at every file here to see what it does but here is a simple overview: 
- The `PropertyMetadata` model holds the annotations we support;
- The `Parser` parses the JMS annotations and builds the PropertyMetadata;
- The `DeserializerGenerator` and `SerializerGenerator`, produces PHP code from the metadata.
  They have some things in common - to avoid duplication they both extend from the abstract generator.
  The generators use twig to render the PHP code, for better readability.
  The indentation in the generated code is not respecting levels of nesting. We could carry around the depth and prepend
  whitespace, but apart from debugging, nobody will look at the generated code.
- The `Compiler` ties everything together:
    - It calls the `Parser` to read JMS metadata into `PropertyMetadata`. It produces a hashmap of class FQN to a
      second hashmap per class with JSON field name to `PropertyMetadata`;
    - It calls the `SerializerGenerator` for the specified API versions and group combinations. The generated files
      follow the naming convention of `serialize_FQN_WITH_UNDERSCORES_group_group_versionnumber.php`;
    - It calls the `DeserializerGenerator`. As we do never deserialize with groups or API version in MAPI, we only
      support the default deserialization. The filename convention is `serialize_FQN_WITH_UNDERSCORES.php`.
- There is a hack to work around the recursive model structure in `Recursion`.
  The proper solution is explained in MAPI-3504.

We decided to not use reflection, for better performance. Properties need to be public or have a public getter for
serialization. For deserialization, we also match constructor arguments by name, so as long as the property name matches
a constructor argument, they need no setter.

## Where do I go for help?

At the moment of writing the people who worked on this are (alphabetic order): 
- David Buchmann 
- Martin Janser
- Michelle Sanver. 

You can always ask any of them in case you need help or have ideas on how to improve and would like some input. 

## Why an object serializer generator

The first experiment was a Golang serializer. It is a lot faster than JMS serializer. However, integrating that with PHP
is some pain, and we would either need some hybrid solution or move a lot of the MAPI logic into Golang.

We experimented and found that using the same concept but with PHP still brings a significant performance gain.
See MAPI-3315.

The PoC gave the following results:
* Overall performance gain: 55%, 390 ms => 175 ms
* CPU and I/O wait both down by ~50%, Memory gain: 21%, 6.5 MB => 5.15 MB

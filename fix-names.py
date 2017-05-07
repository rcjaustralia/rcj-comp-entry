import os


queue = ["."]
queries = ["mail"]
changes = 0

while len(queue) > 0:
    current = queue.pop(0)
    for filename in os.listdir(current):
        filepath = os.path.join(current, filename)

        if os.path.isdir(filepath):
            queue.append(filepath)
            continue

        if filepath.endswith("desktop.ini"):
            # new_name = os.path.join(current, "index.php")
            os.unlink(filepath)
            changes += 1
            print "rm {}".format(filepath)

        if filepath.endswith("default.php"):
            new_name = os.path.join(current, "index.php")
            os.rename(filepath, new_name)
            changes += 1
            print "mv {} -> {}".format(filepath, new_name)

        with open(filepath, "r") as file:
            contents = file.read().lower()

            for query in queries:
                if query in contents:
                    print "matched '{}' in {}".format(query, filepath)

print "{} changes".format(changes)

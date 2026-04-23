import java.util.*;
import java.util.stream.Collectors;

public class MapReduceLogProcessor {

    // --- STEP 1: MAPPER ---
    // Input: A raw log line
    // Output: Key-Value pair (Log Level, 1)
    public static class Mapper {
        public void map(String line, List<KeyValuePair> intermediateResults) {
            String[] parts = line.split(" ");
            if (parts.length > 0) {
                String logLevel = parts[0].toUpperCase(); // Assuming log level is the first word
                intermediateResults.add(new KeyValuePair(logLevel, 1));
            }
        }
    }

    // --- STEP 2: REDUCER ---
    // Input: Key, List of Values
    // Output: Key, Aggregated Sum
    public static class Reducer {
        public KeyValuePair reduce(String key, List<Integer> values) {
            int sum = values.stream().mapToInt(Integer::intValue).sum();
            return new KeyValuePair(key, sum);
        }
    }

    public static void main(String[] args) {
        // Simulated Log File Data
        List<String> logFile = Arrays.asList(
            "INFO User logged in",
            "ERROR Database connection failed",
            "WARN Disk usage at 85%",
            "INFO Data sync complete",
            "ERROR Null pointer exception",
            "INFO User logged out"
        );

        Mapper mapper = new Mapper();
        Reducer reducer = new Reducer();
        List<KeyValuePair> mappedValues = new ArrayList<>();

        // PHASE 1: Mapping
        for (String line : logFile) {
            mapper.map(line, mappedValues);
        }

        // PHASE 2: Shuffle and Sort (Grouping values by key)
        Map<String, List<Integer>> shuffledData = mappedValues.stream()
            .collect(Collectors.groupingBy(
                KeyValuePair::getKey,
                Collectors.mapping(KeyValuePair::getValue, Collectors.toList())
            ));

        // PHASE 3: Reducing
        System.out.println("--- Log Processing Results ---");
        for (String level : shuffledData.keySet()) {
            KeyValuePair result = reducer.reduce(level, shuffledData.get(level));
            System.out.println(result.key + ": " + result.value);
        }
    }

    // Simple Helper Class for Key-Value Pairs
    static class KeyValuePair {
        String key;
        int value;
        KeyValuePair(String key, int value) { this.key = key; this.value = value; }
        String getKey() { return key; }
        int getValue() { return value; }
    }
}

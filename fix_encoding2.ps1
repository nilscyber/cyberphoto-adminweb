$bytes = [System.IO.File]::ReadAllBytes('c:\Dev\git\cyberphoto-adminweb\app\lib\CTradeIn.php')

function Replace-Bytes {
    param([byte[]]$haystack, [byte[]]$needle, [byte[]]$replacement)
    $result = [System.Collections.Generic.List[byte]]::new()
    $i = 0
    while ($i -lt $haystack.Length) {
        $match = $true
        if ($i + $needle.Length -le $haystack.Length) {
            for ($j = 0; $j -lt $needle.Length; $j++) {
                if ($haystack[$i + $j] -ne $needle[$j]) { $match = $false; break }
            }
        } else { $match = $false }
        if ($match) {
            foreach ($b in $replacement) { $result.Add($b) }
            $i += $needle.Length
        } else {
            $result.Add($haystack[$i])
            $i++
        }
    }
    return ,$result.ToArray()
}

# ä = C3 A4 in UTF-8, shown as EF BF BD (U+FFFD) in the file

# "fler ?n" -> "fler än"
$n1 = [byte[]]@(0x66, 0x6C, 0x65, 0x72, 0x20, 0xEF, 0xBF, 0xBD, 0x6E)
$r1 = [byte[]]@(0x66, 0x6C, 0x65, 0x72, 0x20, 0xC3, 0xA4, 0x6E)

# "Inbytesaff?rer" -> "Inbytesaffärer"
$n2 = [byte[]]@(0x49, 0x6E, 0x62, 0x79, 0x74, 0x65, 0x73, 0x61, 0x66, 0x66, 0xEF, 0xBF, 0xBD, 0x72, 0x65, 0x72)
$r2 = [byte[]]@(0x49, 0x6E, 0x62, 0x79, 0x74, 0x65, 0x73, 0x61, 0x66, 0x66, 0xC3, 0xA4, 0x72, 0x65, 0x72)

# "d?r vi saknar" -> "där vi saknar"
$n3 = [byte[]]@(0x64, 0xEF, 0xBF, 0xBD, 0x72, 0x20, 0x76, 0x69, 0x20, 0x73, 0x61, 0x6B, 0x6E, 0x61, 0x72)
$r3 = [byte[]]@(0x64, 0xC3, 0xA4, 0x72, 0x20, 0x76, 0x69, 0x20, 0x73, 0x61, 0x6B, 0x6E, 0x61, 0x72)

$bytes = Replace-Bytes $bytes $n1 $r1
$bytes = Replace-Bytes $bytes $n2 $r2
$bytes = Replace-Bytes $bytes $n3 $r3

[System.IO.File]::WriteAllBytes('c:\Dev\git\cyberphoto-adminweb\app\lib\CTradeIn.php', $bytes)
Write-Host "done"

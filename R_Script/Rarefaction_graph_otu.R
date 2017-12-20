args <- commandArgs(TRUE)
data = read.table(args[1], header = T)

#k = length(grep(x = colnames(data),pattern = "^X2"))
# num <-grep(x = colnames(data),pattern = "^X2")
# print(num[1])
df <- read.table(args[1], header = TRUE)

k <- sample(colnames(df))
r <- grep("^X0.03", k, value = TRUE)
te = 1
name_sam = c()
color_sam = c()
for (i in r) {
    name_sam <- c(name_sam, i)
}
png(args[2], width = 8, height = 6, units = "in", res = 300)
cl <- rainbow(length(r))
for (i in r) {
    if (te == 1) {
        print(i)
        print(data[, i])
        plot(x = data$numsampled, y = data[, i], type = "l", col = cl[te], xlab = "# sequences sampled", ylab = "Observed OTUs", ylim = c(0, 800))#, xlim=c(0,130000), ylim=c(0,900))
        color_sam <- c(color_sam, cl[te])
    }else {
        print(i)
        print(data[, i])
        lines(x = data$numsampled, y = data[, i], type = "l", col = cl[te])
        color_sam <- c(color_sam, cl[te])
    }
    te = te + 1
}

print(name_sam)
print("_-------------_")
print(color_sam)

legend('topleft', name_sam, pch = c(16, 16, 16, 16), lty = c(1, 1, 1, 1), col = c(color_sam, ncol = 1), cex = 0.8)
dev.off()
